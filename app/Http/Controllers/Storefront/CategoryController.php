<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Request $request, Category $category)
    {
        abort_unless($category->is_active, 404);

        $category->load(['children' => fn ($q) => $q->active()->ordered()]);

        // A main category (e.g. "Food Menu") shows every dish from its
        // sub-categories too, not just dishes assigned directly to it —
        // matches how scopeAssignable() already prevents dishes being
        // assigned straight to a main category in the first place.
        $categoryIds = $category->isTopLevel()
            ? $category->children->pluck('id')->push($category->id)
            : collect([$category->id]);

        $dishes = MenuItem::with(['category', 'primaryImage'])
            ->available()
            ->whereIn('category_id', $categoryIds)
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->search.'%'))
            ->when($request->input('sort') === 'price_low', fn ($q) => $q->orderBy('price'))
            ->when($request->input('sort') === 'price_high', fn ($q) => $q->orderByDesc('price'))
            ->when($request->input('sort') === 'latest', fn ($q) => $q->latest())
            ->when(! $request->filled('sort') || $request->input('sort') === 'name', fn ($q) => $q->orderBy('name'))
            ->paginate(12)
            ->withQueryString();

        return view('storefront.menu-list', [
            'dishes'         => $dishes,
            'categories'     => MenuController::navCategories(),
            'recentDishes'   => MenuController::recentDishes(),
            'pageTitle'      => $category->name,
            'activeCategory' => $category,
        ]);
    }
}