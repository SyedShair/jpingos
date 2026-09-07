<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Deal;
use App\Models\MenuItem;
use App\Models\Slider;

class HomeController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */
        $categories = Category::topLevel()
            ->active()
            ->ordered()
            ->with([
                'children' => fn ($q) => $q->active()->ordered()
            ])
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Featured Dishes
        |--------------------------------------------------------------------------
        */
        $featured = MenuItem::with([
                'category',
                'primaryImage'
            ])
            ->available()
            ->where('is_featured', true)
            ->latest()
            ->take(20)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | New Dishes
        |--------------------------------------------------------------------------
        */
        $newArrivals = MenuItem::with([
                'category',
                'primaryImage'
            ])
            ->available()
            ->latest()
            ->take(20)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Products On Sale
        |--------------------------------------------------------------------------
        */
        $onSale = MenuItem::with([
                'category',
                'primaryImage'
            ])
            ->available()
            ->whereNotNull('discount_price')
            ->latest()
            ->take(20)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Flash Deals
        |--------------------------------------------------------------------------
        */
        $flashDeals = Deal::query()
            ->where('type', 'flash_deal')
            ->where('is_active', true)
            ->whereNotNull('ends_at')
            ->where('ends_at', '>', now())
            ->with('appliesToItems.menuItem.primaryImage')
            ->orderBy('ends_at')
            ->take(4)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Deal Item IDs
        |--------------------------------------------------------------------------
        */
        $dealItemIds = MenuItem::available()
            ->whereNotNull('discount_price')
            ->pluck('id')
            ->merge(
                $flashDeals->flatMap(
                    fn ($deal) => $deal->appliesToItems->pluck('menu_item_id')
                )
            )
            ->unique();


        /*
        |--------------------------------------------------------------------------
        | Category Banners
        |
        | Removed take(3), so ALL available category banners can appear.
        |--------------------------------------------------------------------------
        */
        $categoryBanners = Category::topLevel()
            ->active()
            ->whereHas(
                'media',
                fn ($q) => $q->whereNotNull('image')
            )
            ->with('media')
            ->ordered()
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Sliders
        |--------------------------------------------------------------------------
        */
        $sliders = Slider::active()
            ->ordered()
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Daily Deals
        |--------------------------------------------------------------------------
        */
        $dailyDealTypes = array_keys(
            Deal::typeGroups()['Daily & Time-Based Deals']
        );

        $dailyDealsByType = Deal::query()
            ->whereIn('type', $dailyDealTypes)
            ->where('is_active', true)
            ->with('appliesToItems.menuItem.primaryImage')
            ->get()
            ->filter(
                fn ($deal) => $deal->isCurrentlyActive()
            )
            ->groupBy('type');


        /*
        |--------------------------------------------------------------------------
        | Daily Deal Cards
        |--------------------------------------------------------------------------
        */
        $dailyDealCards = collect($dailyDealTypes)
            ->mapWithKeys(function ($type) use ($dailyDealsByType) {

                $cards = ($dailyDealsByType->get($type) ?? collect())
                    ->flatMap(
                        fn ($deal) => $deal->appliesToItems
                            ->filter(
                                fn ($dealItem) =>
                                    $dealItem->menuItem &&
                                    $dealItem->menuItem->is_available
                            )
                            ->map(
                                fn ($dealItem) => [
                                    'deal' => $deal,
                                    'item' => $dealItem->menuItem,
                                ]
                            )
                    )
                    ->unique(
                        fn ($card) => $card['item']->id
                    )
                    ->values();

                return [
                    $type => $cards
                ];
            })
            ->filter(
                fn ($cards) => $cards->isNotEmpty()
            );


        /*
        |--------------------------------------------------------------------------
        | Daily Deals For Product List
        |
        | Converts:
        |
        | type => [
        |     ['deal' => ..., 'item' => ...]
        | ]
        |
        | into one simple collection for the 4-column list.
        |--------------------------------------------------------------------------
        */
        $dailyDealProducts = $dailyDealCards
            ->flatten(1)
            ->unique(
                fn ($card) => $card['item']->id
            )
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Family Deals / Combos
        |--------------------------------------------------------------------------
        */
        $familyDeals = Deal::query()
            ->whereIn('type', ['combo', 'bundle'])
            ->where('is_active', true)
            ->with('bundleComponents.menuItem.primaryImage')
            ->get()
            ->filter(
                fn ($deal) =>
                    $deal->isCurrentlyActive() &&
                    $deal->bundleComponents->isNotEmpty()
            )
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Family Deal Products
        |
        | A combo/bundle deal is priced and sold as ONE unit (combo_price),
        | not as a discount on each component individually — there's no
        | such thing as "the per-item deal price" for a bundle. This used
        | to flatten each deal into one card per component item and then
        | try to compute a per-item "discounted price" from it, which
        | silently fell back to the item's normal price every time
        | (Deal::discountedPriceFor() has no case for the 'fixed_price'
        | discount_type that combo/bundle deals are saved with) — so the
        | deal itself never actually showed, only the plain item.
        |
        | Fixed by keeping ONE card per bundle instead, using the
        | bundle-level helpers already on the Deal model
        | (bundleThumbnailUrl(), bundleItemNames(), bundleOriginalPrice())
        | that were built for exactly this and just weren't being used.
        |--------------------------------------------------------------------------
        */
        $familyDealProducts = $familyDeals->values();


        /*
        |--------------------------------------------------------------------------
        | Homepage
        |--------------------------------------------------------------------------
        */
        return view('storefront.home', [

            'categories'        => $categories,

            'newArrivals'       => $newArrivals,

            'featured'          => $featured,

            'onSale'            => $onSale,

            'flashDeals'        => $flashDeals,

            'dealItemIds'       => $dealItemIds,

            'sliders'           => $sliders,

            'categoryBanners'   => $categoryBanners,

            'dailyDealCards'    => $dailyDealCards,

            'dailyDealProducts' => $dailyDealProducts,

            'familyDeals'       => $familyDeals,

            'familyDealProducts'=> $familyDealProducts,
        ]);
    }



public function quickview(MenuItem $menuItem)
{
    abort_unless($menuItem->is_available, 404);

    $menuItem->load(['category', 'images', 'optionGroups.values']);

    $deal = Deal::query()
        ->whereHas('appliesToItems', fn ($q) => $q->where('menu_item_id', $menuItem->id))
        ->where('is_active', true)
        ->get()
        ->first(fn ($d) => $d->isCurrentlyActive());

    return response()->json([
        'name'        => $menuItem->name,
        'slug'        => $menuItem->slug,
        'category'    => $menuItem->category?->name,
        'description' => $menuItem->description,
        'price'       => number_format((float) $menuItem->price, 2),
        'base_price'  => (float) $menuItem->price, // raw number, used for live JS price math
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

        'deal_price'     => $deal ? number_format($deal->discountedPriceFor((float) $menuItem->price), 2) : null,
        'deal_base_price' => $deal ? (float) $deal->discountedPriceFor((float) $menuItem->price) : null, // raw
        'deal_countdown' => $deal?->countdownTarget()?->format('Y/m/d H:i:s'),
    ]);
}
}