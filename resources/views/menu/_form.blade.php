@csrf

@if ($errors->hasAny(array_filter(array_keys($errors->toArray()), fn ($k) => str_starts_with($k, 'new_options.') || str_starts_with($k, 'option_group_ids'))))
  <div class="alert alert-danger">
    <strong>Please fix the following:</strong>
    <ul class="mb-0">
      @foreach ($errors->toArray() as $field => $messages)
        @if (str_starts_with($field, 'new_options.') || str_starts_with($field, 'option_group_ids'))
          @foreach ($messages as $message)
            <li>{{ $message }}</li>
          @endforeach
        @endif
      @endforeach
    </ul>
  </div>
@endif

<div class="row g-4">

  {{-- Left column: general info --}}
  <div class="col-lg-8">

    <div class="card rounded-4 mb-4">
      <div class="card-body">
        <h6 class="fw-bold mb-3">Dish Information</h6>

        <div class="row g-3">
          <div class="col-12">
            <label for="name" class="form-label">Dish Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $item->name) }}"
              class="form-control @error('name') is-invalid @enderror"
              placeholder="e.g. Grilled Salmon" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-12">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" rows="4"
              class="form-control @error('description') is-invalid @enderror"
              placeholder="Short description shown to customers — how it's prepared, what makes it special...">{{ old('description', $item->description) }}</textarea>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
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
                <option value="" disabled>No categories yet — create a main category and at least one sub-category first</option>
              @endforelse
            </select>
            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label for="prep_time_minutes" class="form-label">Prep Time (minutes)</label>
            <input type="number" name="prep_time_minutes" id="prep_time_minutes" min="0" max="600"
              value="{{ old('prep_time_minutes', $item->prep_time_minutes) }}"
              class="form-control @error('prep_time_minutes') is-invalid @enderror" placeholder="e.g. 15">
            @error('prep_time_minutes') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-12">
            <label for="ingredients" class="form-label">Ingredients</label>
            <textarea name="ingredients" id="ingredients" rows="3"
              class="form-control @error('ingredients') is-invalid @enderror"
              placeholder="Comma-separated, e.g. Salmon, lemon, dill, olive oil">{{ old('ingredients', $item->ingredients) }}</textarea>
            @error('ingredients') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card rounded-4 mb-4">
      <div class="card-body">
        <h6 class="fw-bold mb-3">Pricing</h6>

        <div class="row g-3">
          <div class="col-md-6">
            <label for="price" class="form-label">Price ($)</label>
            <input type="number" step="0.01" min="0" name="price" id="price"
              value="{{ old('price', $item->price) }}"
              class="form-control @error('price') is-invalid @enderror" placeholder="0.00" required>
            @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label for="discount_price" class="form-label">Discount Price ($) <span class="text-muted">(optional)</span></label>
            <input type="number" step="0.01" min="0" name="discount_price" id="discount_price"
              value="{{ old('discount_price', $item->discount_price) }}"
              class="form-control @error('discount_price') is-invalid @enderror" placeholder="0.00">
            @error('discount_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card rounded-4 mb-4">
      <div class="card-body">
        <h6 class="fw-bold mb-3">Dietary &amp; Spice</h6>

        <div class="row g-3">
          <div class="col-md-4">
            <label for="spice_level" class="form-label">Spice Level</label>
            <select name="spice_level" id="spice_level" class="form-select @error('spice_level') is-invalid @enderror">
              @foreach ($spiceLevels as $value => $label)
                <option value="{{ $value }}" @selected(old('spice_level', $item->spice_level) == $value)>{{ $label }}</option>
              @endforeach
            </select>
            @error('spice_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-8">
            <label class="form-label d-block">Dietary Tags</label>
            <div class="d-flex flex-wrap gap-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_vegetarian" id="is_vegetarian" value="1"
                  @checked(old('is_vegetarian', $item->is_vegetarian))>
                <label class="form-check-label" for="is_vegetarian">Vegetarian</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_vegan" id="is_vegan" value="1"
                  @checked(old('is_vegan', $item->is_vegan))>
                <label class="form-check-label" for="is_vegan">Vegan</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_gluten_free" id="is_gluten_free" value="1"
                  @checked(old('is_gluten_free', $item->is_gluten_free))>
                <label class="form-check-label" for="is_gluten_free">Gluten-Free</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Customer customizations: pick existing shared options, or create new ones inline. --}}
    <div class="card rounded-4 mb-4">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h6 class="fw-bold mb-0">Customer Customizations</h6>
          <button type="button" class="btn btn-sm btn-outline-primary" id="add-option-group">
            <i class="material-icons-outlined fs-6 align-middle">add</i> Create New Option
          </button>
        </div>

        @php
          $attachedIds = ($item->optionGroups ?? collect())->pluck('id')->toArray();
        @endphp

        @if ($availableOptionGroups->isNotEmpty())
          <p class="text-muted small mb-2">Select from options already used on other dishes:</p>
          <div class="row g-2 mb-3">
            @foreach ($availableOptionGroups as $group)
              <div class="col-md-6">
                <div class="form-check border rounded-3 p-2">
                  <input class="form-check-input" type="checkbox" name="option_group_ids[]" value="{{ $group->id }}"
                    id="existing_og_{{ $group->id }}"
                    @checked(in_array($group->id, old('option_group_ids', $attachedIds)))>
                  <label class="form-check-label w-100" for="existing_og_{{ $group->id }}">
                    <span class="fw-semibold">{{ $group->name }}</span>
                    <span class="text-muted small d-block">
                      {{ $group->values->pluck('name')->implode(', ') ?: 'No choices yet' }}
                    </span>
                  </label>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <p class="text-muted small mb-3">No reusable options yet — create your first one below.</p>
        @endif

        <p class="text-muted small mb-2">Or create a brand-new option for this dish (it'll be reusable on future dishes too):</p>

        <div id="option-groups-container"></div>
      </div>
    </div>

    {{-- Photo gallery: markup shared by create/edit, behavior wired up
         differently by each page's @push('scripts') block below. --}}
    <div class="card rounded-4 mb-4">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h6 class="fw-bold mb-0">Photo Gallery</h6>
          <span class="text-muted small">Drag thumbnails to reorder · click the star to set the cover photo</span>
        </div>

        <div id="dropzone-gallery" class="dropzone rounded-4"></div>

        <div id="gallery-thumbs" class="d-flex flex-wrap gap-3 mt-3"></div>

        <input type="hidden" name="image_order" id="image_order" value="">
      </div>
    </div>

  </div>

  {{-- Right column: status --}}
  <div class="col-lg-4">

    <div class="card rounded-4 mb-4">
      <div class="card-body">
        <h6 class="fw-bold mb-3">Status</h6>

        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" name="is_available" id="is_available" value="1"
            @checked(old('is_available', $item->exists ? $item->is_available : true))>
          <label class="form-check-label" for="is_available">Available on menu</label>
        </div>

        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1"
            @checked(old('is_featured', $item->is_featured))>
          <label class="form-check-label" for="is_featured">Feature this dish</label>
        </div>
      </div>
    </div>

    <div class="d-grid gap-2">
      <button type="submit" class="btn btn-grd-primary py-2">
        {{ $item->exists ? 'Save Changes' : 'Add Dish to Menu' }}
      </button>
      <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary py-2">Cancel</a>
    </div>

  </div>

</div>

@push('scripts')
<script>
(function () {
  const container = document.getElementById('option-groups-container');
  let groupCounter = 0;

  function valueRowHtml(groupIndex, valueIndex) {
    return `
      <div class="row g-2 align-items-center mb-2 option-value">
        <div class="col-md-6">
          <input type="text" name="new_options[${groupIndex}][values][${valueIndex}][name]"
            class="form-control form-control-sm" placeholder="Choice name, e.g. BBQ">
        </div>
        <div class="col-md-4">
          <div class="input-group input-group-sm">
            <span class="input-group-text">$</span>
            <input type="number" step="0.01" min="0"
              name="new_options[${groupIndex}][values][${valueIndex}][price_delta]"
              value="0.00" class="form-control" placeholder="0.00">
          </div>
        </div>
        <div class="col-md-2 text-end">
          <button type="button" class="btn btn-sm btn-outline-danger remove-value">
            <i class="material-icons-outlined fs-6 align-middle">close</i>
          </button>
        </div>
      </div>`;
  }

  function groupRowHtml(groupIndex) {
    return `
      <div class="option-group border rounded-3 p-3 mb-3" data-group data-value-counter="0">
        <div class="row g-2 align-items-center mb-2">
          <div class="col-md-5">
            <input type="text" name="new_options[${groupIndex}][name]"
              class="form-control form-control-sm" placeholder="Option name, e.g. Sauce" required>
          </div>
          <div class="col-md-3">
            <select name="new_options[${groupIndex}][selection_type]" class="form-select form-select-sm">
              <option value="single">Pick One</option>
              <option value="multiple">Pick Multiple</option>
            </select>
          </div>
          <div class="col-md-3 form-check form-check-sm mt-1">
            <input type="checkbox" class="form-check-input" name="new_options[${groupIndex}][required]" value="1" id="req_${groupIndex}">
            <label class="form-check-label small" for="req_${groupIndex}">Required</label>
          </div>
          <div class="col-md-1 text-end">
            <button type="button" class="btn btn-sm btn-outline-danger remove-group">
              <i class="material-icons-outlined fs-6 align-middle">delete</i>
            </button>
          </div>
        </div>
        <div class="option-values ms-3"></div>
        <button type="button" class="btn btn-sm btn-outline-secondary add-value ms-3 mt-1">
          <i class="material-icons-outlined fs-6 align-middle">add</i> Add Choice
        </button>
      </div>`;
  }

  document.getElementById('add-option-group').addEventListener('click', function () {
    const div = document.createElement('div');
    div.innerHTML = groupRowHtml(groupCounter);
    container.appendChild(div.firstElementChild);
    groupCounter++;
  });

  container.addEventListener('click', function (e) {
    if (e.target.closest('.remove-group')) {
      e.target.closest('.option-group').remove();
    }

    if (e.target.closest('.remove-value')) {
      e.target.closest('.option-value').remove();
    }

    if (e.target.closest('.add-value')) {
      const group = e.target.closest('.option-group');
      const groupIndex = group.querySelector('input[name^="new_options"]').name.match(/new_options\[(\d+)\]/)[1];
      const valueIndex = group.dataset.valueCounter ? parseInt(group.dataset.valueCounter, 10) : group.querySelectorAll('.option-value').length;

      const div = document.createElement('div');
      div.innerHTML = valueRowHtml(groupIndex, valueIndex);
      group.querySelector('.option-values').appendChild(div.firstElementChild);
      group.dataset.valueCounter = valueIndex + 1;
    }
  });
})();
</script>
@endpush