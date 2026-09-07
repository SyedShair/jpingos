@csrf

<div class="card rounded-4 mb-4">
  <div class="card-body">
    <div class="row g-3">
<div class="col-md-6">
  <label for="parent_id" class="form-label">Parent Category</label>
  <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
    <option value="">— None (this is a main category) —</option>
    @foreach ($mainCategories as $main)
      <option value="{{ $main->id }}" @selected((int) old('parent_id', $category->parent_id) === $main->id)>
        {{ $main->name }}
      </option>
    @endforeach
  </select>
  @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
  <p class="form-text mb-0">Leave blank for top-level menus like Food, Drinks, Desserts.</p>
</div>
      <div class="col-md-6">
        <label for="name" class="form-label">Category Name</label>
        <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}"
          class="form-control @error('name') is-invalid @enderror"
          placeholder="e.g. Starters" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-6">
        <label for="icon" class="form-label">Icon <span class="text-muted">(optional, Material Icons name)</span></label>
        <input type="text" name="icon" id="icon" value="{{ old('icon', $category->icon) }}"
          class="form-control @error('icon') is-invalid @enderror"
          placeholder="e.g. restaurant, local_bar, icecream">
        @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <p class="form-text mb-0">
          Browse names at
          <a href="https://fonts.google.com/icons?icon.style=Outlined" target="_blank" rel="noopener">
            fonts.google.com/icons
          </a> — used for menu tabs on the customer site later.
        </p>
      </div>

      <div class="col-12">
        <label for="description" class="form-label">Description <span class="text-muted">(optional)</span></label>
        <input type="text" name="description" id="description" value="{{ old('description', $category->description) }}"
          class="form-control @error('description') is-invalid @enderror"
          placeholder="Shown as a subtitle under the category name on the customer site">
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-6">
        <label for="sort_order" class="form-label">Display Order</label>
        <input type="number" name="sort_order" id="sort_order" min="0"
          value="{{ old('sort_order', $category->sort_order ?? 0) }}"
          class="form-control @error('sort_order') is-invalid @enderror">
        <p class="form-text mb-0">Lower numbers appear first (e.g. Starters = 0, Mains = 1, Desserts = 2).</p>
        @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-6 d-flex align-items-end">
        <div class="form-check form-switch mb-2">
          <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
            @checked(old('is_active', $category->exists ? $category->is_active : true))>
          <label class="form-check-label" for="is_active">Active (visible on the menu)</label>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="d-flex gap-2">
  <button type="submit" class="btn btn-grd-primary px-4">
    {{ $category->exists ? 'Save Changes' : 'Create Category' }}
  </button>
  <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
</div>
