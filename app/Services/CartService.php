<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\MenuItem;
use App\Models\OptionValue;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected const SESSION_KEY = 'cart';

    /**
     * All raw rows, keyed by row_id. Each row is a plain array:
     *   - dish row:   ['row_id', 'menu_item_id', 'deal_id' => null|int, 'quantity', 'option_value_ids']
     *   - bundle row: ['row_id', 'menu_item_id' => null, 'deal_id', 'quantity', 'option_value_ids' => []]
     * Every row always has all five keys, even when the value is null —
     * that's what the original bug was missing, and why an unguarded
     * $row['menu_item_id'] blew up on bundle rows.
     *
     * NOTE: a dish row can now carry a non-null deal_id too (e.g. a
     * flash_deal or bogo item added via add()) — that's different from
     * a bundle row, which has deal_id set AND menu_item_id null.
     */
    protected function rows(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    protected function saveRows(array $rows): void
    {
        Session::put(self::SESSION_KEY, $rows);
    }

    public function add(int $menuItemId, int $quantity = 1, array $optionIds = [], ?int $dealId = null): void
    {
        sort($optionIds);
        $rowId = $this->makeRowId($menuItemId, $optionIds, $dealId);

        $rows = $this->rows();

        if (isset($rows[$rowId])) {
            $rows[$rowId]['quantity'] += $quantity;
        } else {
            $rows[$rowId] = [
                'row_id'           => $rowId,
                'menu_item_id'     => $menuItemId,
                'deal_id'          => $dealId,
                'quantity'         => $quantity,
                'option_value_ids' => $optionIds,
            ];
        }

        $this->saveRows($rows);
    }

    /**
     * Add a combo/bundle deal as its own row. No menu_item_id — priced
     * from the deal's combo price, not summed from components.
     *
     * Only appropriate for deal types where the deal itself IS the
     * product (type === 'combo' or 'bundle'). Every other deal type has
     * a real underlying MenuItem and should go through add() instead,
     * so its discount can be applied to that item's actual price.
     */
    public function addBundle(int $dealId, int $quantity = 1): void
    {
        $rowId = $this->makeRowId(null, [], $dealId);

        $rows = $this->rows();

        if (isset($rows[$rowId])) {
            $rows[$rowId]['quantity'] += $quantity;
        } else {
            $rows[$rowId] = [
                'row_id'           => $rowId,
                'menu_item_id'     => null,
                'deal_id'          => $dealId,
                'quantity'         => $quantity,
                'option_value_ids' => [],
            ];
        }

        $this->saveRows($rows);
    }

    public function updateQuantity(string $rowId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($rowId);
            return;
        }

        $rows = $this->rows();

        if (isset($rows[$rowId])) {
            $rows[$rowId]['quantity'] = $quantity;
            $this->saveRows($rows);
        }
    }

    public function remove(string $rowId): void
    {
        $rows = $this->rows();
        unset($rows[$rowId]);
        $this->saveRows($rows);
    }

    public function count(): int
    {
        return collect($this->rows())->sum('quantity');
    }

    public function subtotal(): float
    {
        return $this->detailedItems()->sum('line_total');
    }

    /**
     * Resolves every row against the DB and returns a uniform object for
     * each. Critically, ->menuItem is ALWAYS present with ->slug, ->name,
     * and ->image_url — the offcanvas partial reads $cartItem->menuItem->slug
     * directly, so a bundle row without a real MenuItem gets a stand-in
     * object built from its Deal instead of a bare stdClass missing slug.
     */
    public function detailedItems(): \Illuminate\Support\Collection
    {
        $rows = collect($this->rows());

        $menuItemIds = $rows->pluck('menu_item_id')->filter()->unique();
        $dealIds     = $rows->pluck('deal_id')->filter()->unique();
        $optionIds   = $rows->flatMap(fn ($r) => $r['option_value_ids'] ?? [])->unique();

        $menuItems = MenuItem::whereIn('id', $menuItemIds)->get()->keyBy('id');
        $deals     = Deal::whereIn('id', $dealIds)->get()->keyBy('id');
        $options   = OptionValue::whereIn('id', $optionIds)->get()->keyBy('id');

        return $rows->map(function (array $row) use ($menuItems, $deals, $options) {
            $menuItemId = $row['menu_item_id'] ?? null;
            $dealId     = $row['deal_id'] ?? null;

            $selectedOptions = collect($row['option_value_ids'] ?? [])
                ->map(fn ($id) => $options->get($id))
                ->filter()
                ->values();

            if ($dealId && ! $menuItemId) {
                // Bundle row — price comes from the deal. Build a
                // menuItem-shaped stand-in so the view's
                // $cartItem->menuItem->{slug,name,image_url} never breaks.
                // NOTE: this points at storefront.dish with the deal's
                // slug, which is almost certainly the wrong route for a
                // bundle — you'll want a storefront.deal route and to
                // branch on $cartItem->is_bundle in the view's <a href>.
                $deal = $deals->get($dealId);

                if (! $deal) {
                    return null; // deal was deleted/unavailable since it was added
                }

                return (object) [
                    'row_id'     => $row['row_id'],
                    'menuItem'   => (object) [
                        'slug'      => $deal->slug ?? '',
                        'name'      => $deal->name,
                        'image_url' => $deal->image_url ?? null,
                    ],
                    'quantity'   => $row['quantity'],
                    'unit_price' => $deal->combo_price,
                    'line_total' => $deal->combo_price * $row['quantity'],
                    'options'    => collect(),
                    'is_bundle'  => true,
                    'deal_id'    => $deal->id,
                    'deal_name'  => $deal->name,
                ];
            }

            // Regular dish row — possibly deal-linked (flash_deal, bogo,
            // free_gift, etc.), possibly a plain menu item with no deal.
            $menuItem = $menuItemId ? $menuItems->get($menuItemId) : null;

            if (! $menuItem) {
                return null; // dish was deleted/unavailable since it was added
            }

            $deal = $dealId ? $deals->get($dealId) : null;

            $baseUnitPrice = $this->resolveDealUnitPrice($deal, $menuItem);
            $unitPrice     = $baseUnitPrice + $selectedOptions->sum('price_adjustment');

            return (object) [
                'row_id'     => $row['row_id'],
                'menuItem'   => $menuItem,
                'quantity'   => $row['quantity'],
                'unit_price' => $unitPrice,
                'line_total' => $unitPrice * $row['quantity'],
                'options'    => $selectedOptions,
                'is_bundle'  => false,
                'deal_id'    => $deal?->id,
                'deal_name'  => $deal?->name,
            ];
        })->filter()->values();
    }

    /**
     * The per-deal-type pricing rule for a single dish row that's linked
     * to a deal. This is the piece that was missing entirely before —
     * every deal-linked row was being forced through addBundle()'s
     * combo_price, which is null/0 for anything that isn't an actual
     * combo/bundle.
     *
     *  - flash_deal / happy_hour / lunch_special: the deal's discount
     *    applied to this item's price (mirrors discountedPriceFor() usage
     *    already on the deals listing page).
     *  - free_gift: the item is free by definition — 0, not a computed
     *    discount off whatever discount_type happens to be set.
     *  - bogo: the "buy" item stays at full price. The "get" item's
     *    discount is a property of a SECOND unit, not this line — cart
     *    doesn't yet model "buy 2 get 1 at X% off" as a split price
     *    within one row, so this deliberately does NOT discount here.
     *    Flagging this as a known gap rather than faking a number.
     *  - anything else (no deal, or an unrecognized type): full price.
     */
    protected function resolveDealUnitPrice(?Deal $deal, MenuItem $menuItem): float
    {
        $price = (float) $menuItem->price;

        if (! $deal) {
            return $price;
        }
return match ($deal->type) {
    'free_gift' => 0.0,

    'flash_deal',
    'happy_hour',
    'lunch_special' => $deal->discountedPriceFor($price),

    'bogo' => $deal->get_discount_percent >= 100
        ? 0.0
        : $price - ($price * ($deal->get_discount_percent / 100)),

    default => $price,
};
        // return match ($deal->type) {
        //     'free_gift' => 0.0,
        //     'bogo'      => $price,
        //     'flash_deal', 'happy_hour', 'lunch_special' => $deal->discountedPriceFor($price),
        //     default     => $price,
        // };
    }

    protected function makeRowId(?int $menuItemId, array $optionIds, ?int $dealId): string
    {
        if ($dealId && ! $menuItemId) {
            return 'deal-' . $dealId;
        }

        return md5($menuItemId . '-' . implode(',', $optionIds) . '-' . $dealId);
    }
}