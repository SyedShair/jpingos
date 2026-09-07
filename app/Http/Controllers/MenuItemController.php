<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuItemRequest;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\OptionGroup;
use App\Services\MenuItemOptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuItemController extends Controller
{
    public function __construct(
        protected MenuItemOptionService $optionService,
    ) {}

    public function index(Request $request)
    {
        $items = MenuItem::query()
            ->with(['category.parent', 'primaryImage'])
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->category))
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->search.'%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('menu.index', [
            'items'      => $items,
            'categories' => Category::with('parent')->active()->assignable()->ordered()->get(),
        ]);
    }

    public function create()
    {
        return view('menu.create', [
            'item'                  => new MenuItem(),
            'categories'            => Category::topLevel()
                ->active()
                ->with(['children' => fn ($q) => $q->active()->ordered()])
                ->ordered()
                ->get(),
            'spiceLevels'           => MenuItem::spiceLevels(),
            'draftToken'            => (string) Str::uuid(),
            'availableOptionGroups' => OptionGroup::with('values')->orderBy('sort_order')->get(),
        ]);
    }

    public function edit(MenuItem $menuItem)
    {
        return view('menu.edit', [
            'item'        => $menuItem->load(['images', 'optionGroups.values']),
            'categories'  => Category::topLevel()
                ->where(function ($q) use ($menuItem) {
                    $q->active()
                        ->orWhere('id', $menuItem->category?->parent_id)
                        ->orWhere('id', $menuItem->category_id);
                })
                ->with(['children' => function ($q) use ($menuItem) {
                    $q->where(function ($qq) use ($menuItem) {
                        $qq->active()->orWhere('id', $menuItem->category_id);
                    })->ordered();
                }])
                ->ordered()
                ->get(),
            'spiceLevels'           => MenuItem::spiceLevels(),
            'availableOptionGroups' => OptionGroup::with('values')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(MenuItemRequest $request)
    {
        $data = $request->safe()->except(['option_group_ids', 'new_options']);
        $data = $this->withBooleanDefaults($request, $data);

        $item = MenuItem::create($data);

        $this->optionService->sync(
            $item,
            $request->input('option_group_ids', []),
            $request->input('new_options', [])
        );

        $draftToken = $request->input('draft_token');
        if ($draftToken && Str::isUuid($draftToken)) {
            $order = array_filter(explode(',', (string) $request->input('image_order')));
            $this->promoteTempImages($item, $draftToken, $order);
        }

        return redirect()
            ->route('menu.show', $item)
            ->with('status', 'Dish added to the menu.');
    }

    public function show(MenuItem $menuItem)
    {
        return view('menu.show', [
            'item' => $menuItem->load(['category.parent', 'images', 'optionGroups.values']),
        ]);
    }

    public function update(MenuItemRequest $request, MenuItem $menuItem)
    {
        $data = $request->safe()->except(['option_group_ids', 'new_options']);
        $data = $this->withBooleanDefaults($request, $data);

        $menuItem->update($data);

        $this->optionService->sync(
            $menuItem,
            $request->input('option_group_ids', []),
            $request->input('new_options', [])
        );

        return redirect()
            ->route('menu.show', $menuItem)
            ->with('status', 'Dish updated.');
    }

    public function destroy(MenuItem $menuItem)
    {
        foreach ($menuItem->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $menuItem->delete();

        return redirect()
            ->route('menu.index')
            ->with('status', 'Dish removed from the menu.');
    }

    public function toggleAvailability(MenuItem $menuItem)
    {
        $menuItem->update(['is_available' => ! $menuItem->is_available]);

        return response()->json(['is_available' => $menuItem->is_available]);
    }

    public function toggleFeatured(MenuItem $menuItem)
    {
        $menuItem->update(['is_featured' => ! $menuItem->is_featured]);

        return response()->json(['is_featured' => $menuItem->is_featured]);
    }

    /**
     * Checkboxes send no key at all when unchecked, so $request->safe()
     * silently omits is_available/is_featured/etc. from $data whenever an
     * admin unchecks one — leaving the old DB value in place instead of
     * flipping to false. $request->boolean() correctly treats "absent" as
     * false, so we overwrite these keys explicitly before saving.
     */
    protected function withBooleanDefaults(Request $request, array $data): array
    {
        foreach (['is_available', 'is_featured', 'is_vegetarian', 'is_vegan', 'is_gluten_free'] as $field) {
            $data[$field] = $request->boolean($field);
        }

        return $data;
    }

    protected function promoteTempImages(MenuItem $item, string $draftToken, array $orderedFilenames = []): void
    {
        $tempDir = "menu-items/temp/{$draftToken}";
        $files = Storage::disk('public')->files($tempDir);

        if (! empty($orderedFilenames)) {
            usort($files, function ($a, $b) use ($orderedFilenames) {
                $posA = array_search(basename($a), $orderedFilenames, true);
                $posB = array_search(basename($b), $orderedFilenames, true);
                $posA = $posA === false ? PHP_INT_MAX : $posA;
                $posB = $posB === false ? PHP_INT_MAX : $posB;

                return $posA <=> $posB;
            });
        }

        foreach ($files as $index => $file) {
            $newPath = "menu-items/{$item->id}/".basename($file);
            Storage::disk('public')->move($file, $newPath);

            $item->images()->create([
                'path'       => $newPath,
                'sort_order' => $index,
                'is_primary' => $index === 0,
            ]);
        }

        Storage::disk('public')->deleteDirectory($tempDir);
    }



}