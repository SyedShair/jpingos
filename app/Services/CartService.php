<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\OptionValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected string $sessionKey = 'storefront_cart';

    protected function raw(): array
    {
        return Session::get($this->sessionKey, []);
    }

    protected function save(array $rows): void
    {
        Session::put($this->sessionKey, $rows);
    }

    /**
     * Add a dish (optionally with chosen customization option values) to
     * the cart. Two identical selections (same dish + same options)
     * merge into one row with a summed quantity, rather than creating
     * duplicate lines.
     */
    public function add(int $menuItemId, int $quantity, array $optionValueIds = []): void
    {
        $menuItem = MenuItem::available()->findOrFail($menuItemId);

        sort($optionValueIds);
        $rowId = md5($menuItemId.'|'.implode(',', $optionValueIds));

        $optionValues = OptionValue::whereIn('id', $optionValueIds)->get();
        $unitPrice = (float) $menuItem->price + $optionValues->sum(fn ($v) => (float) $v->price_delta);

        $rows = $this->raw();

        if (isset($rows[$rowId])) {
            $rows[$rowId]['quantity'] += $quantity;
        } else {
            $rows[$rowId] = [
                'row_id'            => $rowId,
                'menu_item_id'      => $menuItemId,
                'quantity'          => $quantity,
                'unit_price'        => $unitPrice,
                'option_value_ids'  => $optionValueIds,
            ];
        }

        $this->save($rows);
    }

    public function remove(string $rowId): void
    {
        $rows = $this->raw();
        unset($rows[$rowId]);
        $this->save($rows);
    }

    public function updateQuantity(string $rowId, int $quantity): void
    {
        $rows = $this->raw();

        if (! isset($rows[$rowId])) {
            return;
        }

        if ($quantity < 1) {
            unset($rows[$rowId]);
        } else {
            $rows[$rowId]['quantity'] = $quantity;
        }

        $this->save($rows);
    }

    public function count(): int
    {
        return collect($this->raw())->sum('quantity');
    }

    public function subtotal(): float
    {
        return $this->detailedItems()->sum('line_total');
    }

    /**
     * Cart rows hydrated with their real MenuItem model and OptionValue
     * names, ready for the offcanvas / cart page to render.
     */
    public function detailedItems(): Collection
    {
        $rows = $this->raw();
        if (empty($rows)) {
            return collect();
        }

        $menuItemIds = collect($rows)->pluck('menu_item_id')->unique();
        $menuItems = MenuItem::with('primaryImage')->whereIn('id', $menuItemIds)->get()->keyBy('id');

        $allOptionValueIds = collect($rows)->flatMap(fn ($r) => $r['option_value_ids'])->unique();
        $optionValues = OptionValue::whereIn('id', $allOptionValueIds)->get()->keyBy('id');

        return collect($rows)->map(function ($row) use ($menuItems, $optionValues) {
            $menuItem = $menuItems->get($row['menu_item_id']);
            if (! $menuItem) {
                return null; // dish was deleted/unavailable since it was added
            }

            $options = collect($row['option_value_ids'])
                ->map(fn ($id) => $optionValues->get($id))
                ->filter();

            return (object) [
                'row_id'     => $row['row_id'],
                'quantity'   => $row['quantity'],
                'unit_price' => $row['unit_price'],
                'line_total' => $row['unit_price'] * $row['quantity'],
                'menuItem'   => $menuItem,
                'options'    => $options,
            ];
        })->filter()->values();
    }

    public function clear(): void
    {
        Session::forget($this->sessionKey);
    }
}