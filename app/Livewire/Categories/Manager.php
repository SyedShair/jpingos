<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Manager extends Component
{
    // --- Form state ---
    public ?int $editingId = null;
    public bool $showForm = false;

    // --- Delete confirmation modal state ---
    public ?int $confirmingDeleteId = null;
    public ?string $confirmingDeleteName = null;
    public int $confirmingDeleteDishCount = 0;
    public int $confirmingDeleteChildCount = 0;

    #[Validate('nullable|exists:categories,id')]
    public ?int $parent_id = null;

    #[Validate('required|string|max:100')]
    public string $name = '';

    #[Validate('nullable|string|max:255')]
    public ?string $description = null;

    #[Validate('nullable|string|max:50')]
    public ?string $icon = null;

    #[Validate('nullable|integer|min:0')]
    public int $sort_order = 0;

    public bool $is_active = true;

    public function render()
    {
        return view('livewire.categories.manager', [
            'topLevel' => Category::topLevel()
                ->with(['children' => fn ($q) => $q->withCount('menuItems')])
                ->withCount('menuItems')
                ->ordered()
                ->get(),

            'parentOptions' => Category::topLevel()
                ->ordered()
                ->get(),
        ])->layout('layouts.app');
    }

    public function openCreate(?int $parentId = null): void
    {
        $this->resetForm();
        $this->parent_id = $parentId;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $category = Category::findOrFail($id);

        $this->editingId   = $category->id;
        $this->parent_id   = $category->parent_id;
        $this->name        = $category->name;
        $this->description = $category->description;
        $this->icon        = $category->icon;
        $this->sort_order  = $category->sort_order;
        $this->is_active   = $category->is_active;
        $this->showForm    = true;
    }

    public function save(): void
    {
        $this->validate([
            'parent_id'   => 'nullable|exists:categories,id|different:editingId',
            'name'        => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($this->editingId)],
            'description' => 'nullable|string|max:255',
            'icon'        => 'nullable|string|max:50',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        // A category that already has sub-categories must stay top-level —
        // giving it a parent would orphan its children into unsupported
        // grandchildren under this one-level hierarchy.
        if ($this->editingId && $this->parent_id) {
            $hasChildren = Category::where('parent_id', $this->editingId)->exists();

            if ($hasChildren) {
                $this->addError('parent_id', 'This category has sub-categories and can\'t be moved under another category.');
                $this->dispatch('toast', message: 'This category has sub-categories and can\'t be moved.', type: 'error');

                return;
            }
        }

        $data = [
            'parent_id'   => $this->parent_id,
            'name'        => $this->name,
            'description' => $this->description,
            'icon'        => $this->icon,
            'sort_order'  => $this->sort_order,
            'is_active'   => $this->is_active,
        ];

        if ($this->editingId) {
            Category::findOrFail($this->editingId)->update($data);
            $this->dispatch('toast', message: 'Category updated.', type: 'success');
        } else {
            Category::create($data);
            $this->dispatch('toast', message: 'Category created.', type: 'success');
        }

        $this->resetForm();
    }

    /**
     * Opens the delete-confirmation modal for a given category instead of
     * deleting straight away. $dishCount / $childCount are passed in from
     * the view (already computed there via withCount) so we don't have to
     * re-query them here.
     */
    public function confirmDelete(int $id, string $name, int $dishCount = 0, int $childCount = 0): void
    {
        $this->confirmingDeleteId = $id;
        $this->confirmingDeleteName = $name;
        $this->confirmingDeleteDishCount = $dishCount;
        $this->confirmingDeleteChildCount = $childCount;
    }

    /**
     * Closes the modal without deleting anything.
     */
    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
        $this->confirmingDeleteName = null;
        $this->confirmingDeleteDishCount = 0;
        $this->confirmingDeleteChildCount = 0;
    }

    /**
     * Actually deletes the category the modal is currently confirming.
     * This replaces the old delete(int $id) method — the view no longer
     * calls delete() directly.
     */
    public function deleteConfirmed(): void
    {
        if (! $this->confirmingDeleteId) {
            return;
        }

        $category = Category::findOrFail($this->confirmingDeleteId);
        $dishCount = $category->menuItems()->count();
        $childCount = $category->children()->count();

        $category->delete();

        $message = 'Category deleted.';
        if ($dishCount > 0) {
            $message .= " {$dishCount} dish(es) are now uncategorized.";
        }
        if ($childCount > 0) {
            $message .= " {$childCount} sub-categor(y/ies) are now top-level.";
        }

        $this->dispatch('toast', message: $message, type: 'success');

        $this->confirmingDeleteId = null;
        $this->confirmingDeleteName = null;
        $this->confirmingDeleteDishCount = 0;
        $this->confirmingDeleteChildCount = 0;
    }

    public function toggleActive(int $id): void
    {
        $category = Category::findOrFail($id);
        $category->update(['is_active' => ! $category->is_active]);

        $this->dispatch(
            'toast',
            message: $category->is_active ? "\"{$category->name}\" is now visible." : "\"{$category->name}\" is now hidden.",
            type: 'success'
        );
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'parent_id', 'name', 'description', 'icon', 'sort_order', 'is_active', 'showForm']);
        $this->sort_order = 0;
        $this->is_active = true;
        $this->resetErrorBag();
    }
}