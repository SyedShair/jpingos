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

$dealItemIds = collect();

        if ($related->isNotEmpty()) {
            $dealItemIds = Deal::where('is_active', true)
                ->whereHas('appliesToItems', fn ($q) => $q->whereIn('menu_item_id', $related->pluck('id')))
                ->with(['appliesToItems' => fn ($q) => $q->whereIn('menu_item_id', $related->pluck('id'))])
                ->get()
                ->pluck('appliesToItems')
                ->flatten()
                ->pluck('menu_item_id');
        }



    return view('storefront.dish', [
        'item'       => $menuItem,
        'deal'       => $deal,
        'related'    => $related,
        'categories' => Category::topLevel()
            ->active()
            ->ordered()
            ->with(['children' => fn ($q) => $q->active()->ordered()])
            ->get(),
        'dealItemIds' => $dealItemIds,
    ]);
}
}