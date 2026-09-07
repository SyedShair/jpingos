@csrf

<div class="card rounded-4 mb-4">
  <div class="card-body">
    <div class="row g-3">

      <div class="col-md-6">
        <label for="title" class="form-label">Title</label>
        <input type="text" name="title" id="title" value="{{ old('title', $slider->title) }}"
          class="form-control @error('title') is-invalid @enderror"
          placeholder="e.g. Women New Collection" required>
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-6">
        <label for="subtitle" class="form-label">Subtitle <span class="text-muted">(optional)</span></label>
        <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle', $slider->subtitle) }}"
          class="form-control @error('subtitle') is-invalid @enderror"
          placeholder="e.g. Up to 70% off selected Product">
        @error('subtitle') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-6">
        <label for="button_text" class="form-label">Button Text</label>
        <input type="text" name="button_text" id="button_text"
          value="{{ old('button_text', $slider->button_text ?? 'Shop Now') }}"
          class="form-control @error('button_text') is-invalid @enderror">
        @error('button_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-6">
        <label for="button_url" class="form-label">Button Link</label>
        <input type="text" name="button_url" id="button_url" value="{{ old('button_url', $slider->button_url) }}"
          class="form-control @error('button_url') is-invalid @enderror"
          placeholder="e.g. /shop-grid or https://...">
        @error('button_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-6">
        <label for="sort_order" class="form-label">Display Order</label>
        <input type="number" name="sort_order" id="sort_order" min="0"
          value="{{ old('sort_order', $slider->sort_order ?? 0) }}"
          class="form-control @error('sort_order') is-invalid @enderror">
        <p class="form-text mb-0">Lower numbers show first.</p>
        @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-6 d-flex align-items-end">
        <div class="form-check form-switch mb-2">
          <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
            @checked(old('is_active', $slider->exists ? $slider->is_active : true))>
          <label class="form-check-label" for="is_active">Active (visible on the site)</label>
        </div>
      </div>

      <div class="col-12">
        <label for="image" class="form-label">
          Slide Image {{ $slider->exists ? '' : '(recommended: 1920×800px)' }}
        </label>
        <input type="file" name="image" id="image" accept="image/*"
          class="form-control @error('image') is-invalid @enderror">
        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror

        @if ($slider->exists)
          <div class="mt-3">
            <img src="{{ $slider->image_url }}" alt="{{ $slider->title }}"
              class="rounded-3" style="max-width:320px; max-height:150px; object-fit:cover;">
            <p class="text-muted small mb-0">Current image — upload a new one to replace it.</p>
          </div>
        @endif
      </div>

    </div>
  </div>
</div>

<div class="d-flex gap-2">
  <button type="submit" class="btn btn-grd-primary px-4">
    {{ $slider->exists ? 'Save Changes' : 'Add Slide' }}
  </button>
  <a href="{{ route('sliders.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
</div>