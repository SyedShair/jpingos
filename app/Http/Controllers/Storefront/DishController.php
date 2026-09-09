<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\MenuItem;
use App\Models\Category;

class DishController extends Controller
{
    public function show(MenuItem $menuItem)
    {
        abort_unless($menuItem->is_available, 404);

        $menuItem->load(['category.parent', 'images', 'optionGroups.values']);

        $deal = Deal::query()
            ->whereHas('appliesToItems', fn ($q) => $q->where('menu_item_id', $menuItem->id))
            ->where('is_active', true)
            ->get()
            ->first(fn ($d) => $d->isCurrentlyActive());

        $related = MenuItem::with(['category', 'primaryImage'])
            ->available()
            ->where('category_id', $menuItem->category_id)
            ->where('id', '!=', $menuItem->id)
            ->inRandomOrder()
            ->take(8)
            ->get();

        // menu_item_id => Deal — only for deals that are live right now.
        // The Blade checks $relatedDeal->countdownTarget() per item to
        // decide whether to actually show a countdown (some deal types,
        // like a recurring Happy Hour with no fixed end, have no target
        // and correctly show no countdown even though they're "on deal").
        $dealsByItemId = collect();

        if ($related->isNotEmpty()) {
            $relatedIds = $related->pluck('id');

            $liveDeals = Deal::where('is_active', true)
                ->whereHas('appliesToItems', fn ($q) => $q->whereIn('menu_item_id', $relatedIds))
                ->with(['appliesToItems' => fn ($q) => $q->whereIn('menu_item_id', $relatedIds)])
                ->get()
                ->filter(fn ($deal) => $deal->isCurrentlyActive());

            foreach ($liveDeals as $liveDeal) {
                foreach ($liveDeal->appliesToItems as $dealItem) {
                    $existing = $dealsByItemId->get($dealItem->menu_item_id);

                    // If two live deals target the same dish, prefer
                    // whichever one has a real countdown; if both (or
                    // neither) do, keep the one that ends soonest.
                    if (! $existing) {
                        $dealsByItemId->put($dealItem->menu_item_id, $liveDeal);
                        continue;
                    }

                    $existingTarget = $existing->countdownTarget();
                    $newTarget = $liveDeal->countdownTarget();

                    if ($newTarget && (! $existingTarget || $newTarget->lt($existingTarget))) {
                        $dealsByItemId->put($dealItem->menu_item_id, $liveDeal);
                    }
                }
            }
        }

        return view('storefront.dish', [
            'item'          => $menuItem,
            'deal'          => $deal,
            'related'       => $related,
            'categories'    => Category::topLevel()
                ->active()
                ->ordered()
                ->with(['children' => fn ($q) => $q->active()->ordered()])
                ->get(),
            'dealsByItemId' => $dealsByItemId,
        ]);
    }
}