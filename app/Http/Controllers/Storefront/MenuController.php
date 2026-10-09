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
            'searchTerm'     => $this->searchTerm($request),
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
            'searchTerm'     => $this->searchTerm($request),
        ]);
    }

    /**
     * Site-wide search — GET /search?q=pizza
     *
     * This is the target of the header's search box. It reuses the same
     * listing view and the same baseQuery() as the menu pages, so sorting,
     * price filters and "Load more" behave identically on results.
     */
    public function search(Request $request)
    {
        $term = $this->searchTerm($request);

        // Nothing typed: show the full menu instead of an empty
        // "results for ''" page.
        if ($term === '') {
            return redirect()->route('storefront.menu.index');
        }

        $dishes = $this->baseQuery($request)->paginate($this->perPage($request))->withQueryString();

        return view('storefront.menu-list', [
            'dishes'         => $dishes,
            'categories'     => self::navCategories(),
            'recentDishes'   => self::recentDishes(),
            'pageTitle'      => 'Search results for "' . $term . '"',
            'activeCategory' => null,
            'searchTerm'     => $term,
        ]);
    }

    /**
     * Shared filter/sort logic for the "all dishes", "single category"
     * and "search" listings, so all three stay in sync.
     */
    protected function baseQuery(Request $request)
    {
        $term = $this->searchTerm($request);
        $sort = $request->input('sort');

        return MenuItem::with(['category', 'primaryImage'])
            ->available()
            ->when($term !== '', fn ($q) => $this->applySearch($q, $term))
            ->when($request->filled('price_min'), function ($q) use ($request) {
                $q->where('price', '>=', (float) $request->input('price_min'));
            })
            ->when($request->filled('price_max'), function ($q) use ($request) {
                $q->where('price', '<=', (float) $request->input('price_max'));
            })
            ->when($sort === 'price_low', fn ($q) => $q->orderBy('price'))
            ->when($sort === 'price_high', fn ($q) => $q->orderByDesc('price'))
            ->when($sort === 'latest', fn ($q) => $q->latest())
            ->when(! in_array($sort, ['price_low', 'price_high', 'latest'], true), function ($q) use ($term) {
                // Default ordering. While searching, dishes whose NAME
                // matches come first; ones that only match in the
                // description, ingredients or category follow.
                if ($term !== '') {
                    $q->orderByRaw('CASE WHEN name LIKE ? THEN 0 ELSE 1 END', [
                        '%' . $this->escapeLike($term) . '%',
                    ]);
                }

                $q->orderBy('name');
            });
    }

    /**
     * The search text, from either ?q= (header search box) or ?search=
     * (the listing page's own filter). Always returns a trimmed string.
     */
    protected function searchTerm(Request $request): string
    {
        foreach (['q', 'search'] as $key) {
            $value = $request->input($key);

            if (is_string($value) && trim($value) !== '') {
                return mb_substr(trim($value), 0, 100);
            }
        }

        return '';
    }

    /**
     * Every word must match somewhere (name, description, ingredients or
     * category name), so "chicken burger" finds "Spicy Chicken Burger"
     * and also a burger whose ingredients list chicken.
     */
    protected function applySearch($query, string $term)
    {
        $words = array_slice(preg_split('/\s+/', $term, -1, PREG_SPLIT_NO_EMPTY), 0, 6);

        foreach ($words as $word) {
            $like = '%' . $this->escapeLike($word) . '%';

            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                  ->orWhere('description', 'like', $like)
                  ->orWhere('ingredients', 'like', $like)
                  ->orWhereHas('category', fn ($c) => $c->where('name', 'like', $like));
            });
        }

        return $query;
    }

    /** Stops a typed % or _ from acting as a SQL wildcard. */
    protected function escapeLike(string $value): string
    {
        return addcslashes($value, '%_\\');
    }


    /**
     * Batch size: how many dishes show initially and how many each
     * "Load more" click adds. Clamped so a hand-edited ?per_page= can't
     * pull the whole menu in one go.
     */
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