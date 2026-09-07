<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /** All dishes across every category — e.g. GET /menu */
    public function index(Request $request)
    {
        $dishes = $this->baseQuery($request)->paginate($this->perPage($request))->withQueryString();

        return view('storefront.menu-list', [
            'dishes'         => $dishes,
            'categories'     => self::navCategories(),
            'recentDishes'   => self::recentDishes(),
            'pageTitle'      => 'Menu',
            'activeCategory' => null,
        ]);
    }

    /** Dishes within a single category — e.g. GET /menu/{category} */
    public function show(Request $request, Category $category)
    {
        $dishes = $this->baseQuery($request)
            ->where('category_id', $category->id)
            ->paginate($this->perPage($request))
            ->withQueryString();

        return view('storefront.menu-list', [
            'dishes'         => $dishes,
            'categories'     => self::navCategories(),
            'recentDishes'   => self::recentDishes(),
            'pageTitle'      => $category->name,
            'activeCategory' => $category,
        ]);
    }

    /**
     * Shared filter/sort logic for both the "all dishes" and
     * "single category" listings, so index() and show() stay in sync.
     */
    protected function baseQuery(Request $request)
    {
        return MenuItem::with(['category', 'primaryImage'])
            ->available()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('price_min'), function ($q) use ($request) {
                $q->where('price', '>=', (float) $request->input('price_min'));
            })
            ->when($request->filled('price_max'), function ($q) use ($request) {
                $q->where('price', '<=', (float) $request->input('price_max'));
            })
            ->when($request->input('sort') === 'price_low', fn ($q) => $q->orderBy('price'))
            ->when($request->input('sort') === 'price_high', fn ($q) => $q->orderByDesc('price'))
            ->when($request->input('sort') === 'latest', fn ($q) => $q->latest())
            ->when(! $request->filled('sort') || $request->input('sort') === 'name', fn ($q) => $q->orderBy('name'));
    }

    /** Per-page count from the toolbar select, clamped to a sane range. */
    protected function perPage(Request $request): int
    {
        $perPage = (int) $request->input('per_page', 12);

        return in_array($perPage, [12, 24, 36], true) ? $perPage : 12;
    }

    /** Shared sidebar data — main categories with active sub-categories nested. */
    public static function navCategories()
    {
        return Category::topLevel()
            ->active()
            ->ordered()
            ->with(['children' => fn ($q) => $q->active()->ordered()])
            ->get();
    }

    public static function recentDishes()
    {
        return MenuItem::with('primaryImage')->available()->latest()->take(3)->get();
    }
}