<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
       $categories = Category::with('parent')
    ->withCount('menuItems')
    ->orderByRaw('COALESCE(parent_id, id)')
    ->orderBy('parent_id')
    ->orderBy('sort_order')
    ->paginate(15);

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create', [
            'category'       => new Category(),
            'mainCategories' => Category::main()->ordered()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        Category::create($data);

        return redirect()
            ->route('categories.index')
            ->with('status', 'Category created.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', [
            'category'       => $category,
            // A category can't become its own parent, and (kept simple,
            // one level deep) subcategories aren't offered as parents.
            'mainCategories' => Category::main()
                ->where('id', '!=', $category->id)
                ->ordered()
                ->get(),
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $data = $this->validated($request, $category);

        // Guard: a category with children must stay a main category —
        // otherwise its children would become orphaned grandchildren,
        // which this one-level hierarchy doesn't support.
        if (! empty($data['parent_id']) && $category->children()->exists()) {
            return back()
                ->withInput()
                ->withErrors(['parent_id' => 'This category has subcategories and can\'t be moved under another category.']);
        }

        $category->update($data);

        return redirect()
            ->route('categories.index')
            ->with('status', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->children()->exists()) {
            return back()->withErrors([
                'category' => 'Delete or reassign its subcategories first.',
            ]);
        }

        $dishCount = $category->menuItems()->count();

        $category->delete();

        $message = $dishCount > 0
            ? "Category deleted. {$dishCount} dish(es) are now uncategorized — assign them a new category."
            : 'Category deleted.';

        return redirect()
            ->route('categories.index')
            ->with('status', $message);
    }

    protected function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'parent_id'   => [
                'nullable',
                'exists:categories,id',
                Rule::notIn([$category?->id]), // can't be its own parent
            ],
            'name'        => [
                'required', 'string', 'max:100',
                Rule::unique('categories', 'name')->ignore($category?->id),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'icon'        => ['nullable', 'string', 'max:50'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
        ]);
    }
}