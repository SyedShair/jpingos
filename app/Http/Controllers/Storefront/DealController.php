<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

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

        // Pull every is_active deal matching search/type first — filtering
        // by isCurrentlyActive() needs the real Deal instance (it checks
        // starts_at/ends_at/recurring_days/daily windows), which can't be
        // expressed as a single SQL WHERE, so it has to happen in PHP
        // after the query runs, before pagination.
        $allMatching = Deal::query()
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

            ->get()
            ->filter(fn ($deal) => $deal->isCurrentlyActive());

        // Manually paginate the filtered collection, since ->paginate()
        // only works at the query-builder level and isCurrentlyActive()
        // can't be pushed down into SQL.
        $page = (int) request('page', 1);
        $deals = new LengthAwarePaginator(
            $allMatching->slice(($page - 1) * $perPage, $perPage)->values(),
            $allMatching->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Type counts should also only reflect live deals, not merely
        // is_active ones, so the sidebar filter list doesn't offer a
        // type with zero actually-visible deals.
        $typeCounts = collect(self::TYPE_LABELS)->mapWithKeys(function ($label, $key) {
            $count = Deal::where('is_active', true)
                ->where('type', $key)
                ->get()
                ->filter(fn ($deal) => $deal->isCurrentlyActive())
                ->count();

            return [$key => $count];
        });

        $recentDeals = Deal::where('is_active', true)
            ->latest()
            ->get()
            ->filter(fn ($deal) => $deal->isCurrentlyActive())
            ->take(8);

        $categories = Category::topLevel()
            ->active()
            ->ordered()
            ->with([
                'children' => fn ($q) => $q->active()->ordered()
            ])
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
            'categories'  => $categories,
        ]);
    }

    public function quickview(Deal $deal)
    {
        abort_unless($deal->is_active, 404);
        abort_unless($deal->isCurrentlyActive(), 404);

        $isBundle = in_array($deal->type, ['combo', 'bundle'], true);

        if ($isBundle) {
            $firstComponent = $deal->bundleComponents->first()?->menuItem;
            abort_unless($firstComponent, 404);

            return response()->json([
                'id'          => null,
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
                'images'      => [$deal->image ? Storage::url($deal->image) : $firstComponent->image_url],
                'url'          => '#',
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
            default => null,
        };

        abort_unless($menuItem, 404);

        $menuItem->load(['category', 'images', 'optionGroups.values']);

        [$dealBasePrice, $dealPriceFormatted] = match ($deal->type) {
            'flash_deal', 'happy_hour', 'lunch_special' => [
                (float) $deal->discountedPriceFor((float) $menuItem->price),
                number_format($deal->discountedPriceFor((float) $menuItem->price), 2),
            ],
            'free_gift' => [0.0, number_format(0, 2)],
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