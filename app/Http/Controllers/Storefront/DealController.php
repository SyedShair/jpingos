<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use Illuminate\Support\Facades\Storage;

class DealController extends Controller
{
    private const TYPE_LABELS = [
        'flash_deal'    => 'Flash Deal',
        'happy_hour'    => 'Happy Hour',
        'lunch_special' => 'Lunch Special',
        'tiered_spend'  => 'Spend & Save',
        'bogo'          => 'Buy One Get One',
        'free_gift'     => 'Free Gift',
        'combo'         => 'Combo',
        'bundle'        => 'Bundle',
        'promo_code'    => 'Promo Code',
    ];

    public function index()
    {
        $search = trim((string) request('search'));
        $type = request('type');
        $sort = request('sort', 'default');

        $perPage = (int) request('per_page', 12);

        if (! in_array($perPage, [12, 24, 30], true)) {
            $perPage = 12;
        }

        $deals = Deal::query()
            ->with([
                'appliesToItems.menuItem',
                'buyItems.menuItem',
                'freeItems.menuItem',
                'bundleComponents.menuItem',
            ])
            ->where('is_active', true)

            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('promo_code', 'like', '%' . $search . '%');
                });
            })

            ->when(
                $type && array_key_exists($type, self::TYPE_LABELS),
                fn ($q) => $q->where('type', $type)
            )

            ->when(
                $sort === 'latest',
                fn ($q) => $q->latest()
            )

            ->when(
                $sort === 'default',
                fn ($q) => $q->orderBy('name')
            )

            ->paginate($perPage)
            ->withQueryString();

        $typeCounts = collect(self::TYPE_LABELS)->mapWithKeys(
            fn ($label, $key) => [
                $key => Deal::where('is_active', true)
                    ->where('type', $key)
                    ->count()
            ]
        );

        $recentDeals = Deal::where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        return view('storefront.deals', [
            'pageTitle'   => 'Deals',
            'deals'       => $deals,
            'typeLabels'  => self::TYPE_LABELS,
            'typeCounts'  => $typeCounts,
            'recentDeals' => $recentDeals,
            'search'      => $search,
            'activeType'  => $type,
            'sort'        => $sort,
            'perPage'     => $perPage,
        ]);
    }

    public function quickview(Deal $deal)
{
    abort_unless($deal->is_active, 404);

    $isBundle = in_array($deal->type, ['combo', 'bundle'], true);

    if ($isBundle) {
        $firstComponent = $deal->bundleComponents->first()?->menuItem;
        abort_unless($firstComponent, 404);

        return response()->json([
            'id'          => null, // no single menu_item_id — bundle rows in cart use deal_id alone
            'name'        => $deal->name,
            'slug'        => $deal->slug,
            'deal_id'     => $deal->id,
            'category'    => $deal->type === 'bundle' ? 'Bundle' : 'Combo',
            'description' => $deal->bundleItemNames(),
            'price'       => number_format($deal->bundleOriginalPrice(), 2),
            'base_price'  => (float) $deal->bundleOriginalPrice(),
            'discount_price' => null,
            'is_on_sale'  => true,
            'spice_level' => null,
            'dietary'     => [],
            'images'      => [Storage::url($deal->image) ?: $firstComponent->image_url],
            'url'          => '#', // no dedicated bundle page yet — see note below
            'wishlist_url' => route('storefront.wishlist'),
            'option_groups' => [],

            'deal_price'      => number_format($deal->combo_price, 2),
            'deal_base_price' => (float) $deal->combo_price,
            'deal_countdown'  => $deal->countdownTarget()?->format('Y/m/d H:i:s'),
        ]);
    }

    $menuItem = match ($deal->type) {
        'flash_deal', 'happy_hour', 'lunch_special' => $deal->appliesToItems->first()?->menuItem,
        'bogo' => $deal->buyItems->first()?->menuItem,
        'free_gift' => $deal->freeItems->first()?->menuItem,
        default => null, // tiered_spend, promo_code — no single item, never linked to from the view
    };

    abort_unless($menuItem, 404);

    $menuItem->load(['category', 'images', 'optionGroups.values']);

    [$dealBasePrice, $dealPriceFormatted] = match ($deal->type) {
        'flash_deal', 'happy_hour', 'lunch_special' => [
            (float) $deal->discountedPriceFor((float) $menuItem->price),
            number_format($deal->discountedPriceFor((float) $menuItem->price), 2),
        ],
        'free_gift' => [0.0, number_format(0, 2)],
        // bogo: the "buy" item stays full price — the "get" item's
        // discount applies to a second unit, not this line.
        default => [null, null],
    };

    return response()->json([
        'id'          => $menuItem->id,
        'name'        => $menuItem->name,
        'slug'        => $menuItem->slug,
        'deal_id'     => $deal->id,
        'category'    => $menuItem->category?->name,
        'description' => $menuItem->description,
        'price'       => number_format((float) $menuItem->price, 2),
        'base_price'  => (float) $menuItem->price,
        'discount_price' => $menuItem->is_on_sale
            ? number_format((float) $menuItem->discount_price, 2)
            : null,
        'is_on_sale'  => $menuItem->is_on_sale,
        'spice_level' => $menuItem->spice_level !== 'none' ? ucfirst($menuItem->spice_level) : null,
        'dietary'     => array_filter([
            $menuItem->is_vegetarian ? 'Vegetarian' : null,
            $menuItem->is_vegan ? 'Vegan' : null,
            $menuItem->is_gluten_free ? 'Gluten-Free' : null,
        ]),
        'images'      => $menuItem->images->isNotEmpty()
            ? $menuItem->images->pluck('url')
            : [$menuItem->image_url],
        'url'          => route('storefront.dish', $menuItem->slug),
        'wishlist_url' => route('storefront.wishlist'),

        'option_groups' => $menuItem->optionGroups->map(function ($group) {
            return [
                'id'       => $group->id,
                'name'     => $group->name,
                'required' => $group->min_select > 0,
                'multiple' => $group->selection_type === 'multiple',
                'values'   => $group->values->map(function ($value) {
                    return [
                        'id'          => $value->id,
                        'name'        => $value->name,
                        'price_delta' => (float) $value->price_delta,
                    ];
                })->values(),
            ];
        })->values(),

        'deal_price'      => $dealPriceFormatted,
        'deal_base_price' => $dealBasePrice,
        'deal_countdown'  => $deal->countdownTarget()?->format('Y/m/d H:i:s'),
    ]);
}
}