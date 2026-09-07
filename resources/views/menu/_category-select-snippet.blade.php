{{--
  Replace the existing <select name="category_id" ...> block in
  resources/views/menu/_form.blade.php with this version, which groups
  dishes under their main category (Food Menu, Drink Menu, Dessert Menu)
  now that categories can be nested.

  $categories passed from the controller should be TOP-LEVEL categories
  with their children eager-loaded, e.g.:
    Category::topLevel()->with('children')->active()->ordered()->get()
--}}
<label for="category_id" class="form-label d-flex align-items-center justify-content-between">
  <span>Category</span>
  <a href="{{ route('categories.index') }}" class="small">+ Manage categories</a>
</label>
<select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
  <option value="" disabled @selected(old('category_id', $item->category_id) === null)>Select a category…</option>

  @forelse ($categories as $main)
    @if ($main->children->isNotEmpty())
      <optgroup label="{{ $main->name }}">
        @foreach ($main->children as $child)
          <option value="{{ $child->id }}" @selected((int) old('category_id', $item->category_id) === $child->id)>
            {{ $child->name }}
          </option>
        @endforeach
      </optgroup>
    @else
      {{-- Main category with no sub-categories yet — usable directly --}}
      <option value="{{ $main->id }}" @selected((int) old('category_id', $item->category_id) === $main->id)>
        {{ $main->name }}
      </option>
    @endif
  @empty
    <option value="" disabled>No categories yet — create one first</option>
  @endforelse
</select>
@error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
