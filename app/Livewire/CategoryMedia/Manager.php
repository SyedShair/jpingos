<?php

namespace App\Livewire\CategoryMedia;

use App\Models\Category;
use App\Models\CategoryMedia;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Manager extends Component
{
    use WithFileUploads;

    public bool $showForm = false;

    /** The category currently being managed. */
    public ?int $category_id = null;

    /** Newly-selected files awaiting upload. */
    public $image = null;
    public $pdf_menu = null;

    /** Existing saved paths, for preview/removal while editing. */
    public ?int $editingMediaId = null;
    public ?string $existing_image_path = null;
    public ?string $existing_pdf_menu_path = null;

   public function render()
{
    // Top-level categories only, each paired with its media row if one
    // exists — sub-categories aren't shown here since banner/PDF media
    // is only managed at the main-category level.
    $categories = Category::with('media')
        ->topLevel()
        ->ordered()
        ->get();

    return view('livewire.category-media.manager', [
        'categories' => $categories,
    ]);
}

    public function manage(int $categoryId): void
    {
        $this->resetForm();

        $category = Category::with('media')->findOrFail($categoryId);
        $this->category_id = $category->id;

        if ($category->media) {
            $this->editingMediaId         = $category->media->id;
            $this->existing_image_path    = $category->media->image;
            $this->existing_pdf_menu_path = $category->media->pdf_menu;
        }

        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|max:2048',      // 2MB
            'pdf_menu'    => 'nullable|mimes:pdf|max:10240',  // 10MB
        ]);

        $data = ['category_id' => $this->category_id];

        if ($this->image) {
            if ($this->existing_image_path) {
                Storage::disk('public')->delete($this->existing_image_path);
            }
            $data['image'] = $this->image->store('category-media/images', 'public');
        }

        if ($this->pdf_menu) {
            if ($this->existing_pdf_menu_path) {
                Storage::disk('public')->delete($this->existing_pdf_menu_path);
            }
            $data['pdf_menu'] = $this->pdf_menu->store('category-media/menus', 'public');
        }

        CategoryMedia::updateOrCreate(['category_id' => $this->category_id], $data);

        session()->flash('status', 'Category media saved.');
        $this->resetForm();
    }

    public function removeImage(): void
    {
        if ($this->editingMediaId && $this->existing_image_path) {
            Storage::disk('public')->delete($this->existing_image_path);
            CategoryMedia::whereKey($this->editingMediaId)->update(['image' => null]);
        }

        $this->existing_image_path = null;
        $this->image = null;
    }

    public function removePdfMenu(): void
    {
        if ($this->editingMediaId && $this->existing_pdf_menu_path) {
            Storage::disk('public')->delete($this->existing_pdf_menu_path);
            CategoryMedia::whereKey($this->editingMediaId)->update(['pdf_menu' => null]);
        }

        $this->existing_pdf_menu_path = null;
        $this->pdf_menu = null;
    }

    /** Remove all media for a category directly from the list (no need to open the form). */
    public function clearMedia(int $categoryId): void
    {
        $media = CategoryMedia::where('category_id', $categoryId)->first();

        if (! $media) {
            return;
        }

        if ($media->image) {
            Storage::disk('public')->delete($media->image);
        }
        if ($media->pdf_menu) {
            Storage::disk('public')->delete($media->pdf_menu);
        }

        $media->delete();

        session()->flash('status', 'Media cleared for this category.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset([
            'category_id', 'image', 'pdf_menu',
            'editingMediaId', 'existing_image_path', 'existing_pdf_menu_path',
            'showForm',
        ]);
        $this->resetErrorBag();
    }
}
