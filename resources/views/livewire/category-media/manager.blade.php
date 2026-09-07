<div>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="row g-4">

    {{-- List of every category with its media status --}}
    <div class="col-lg-{{ $showForm ? 7 : 12 }}">
      <div class="card rounded-4">
        <div class="card-body">
          <h5 class="mb-3">Category Media</h5>
          <p class="text-muted small mb-4">
            Attach a banner image and/or a downloadable PDF menu to any category.
            This is separate from the Categories page — editing here never
            touches a category's name, order, or active status.
          </p>

          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>Category</th>
                  <th>Image</th>
                  <th>PDF Menu</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($categories as $category)
                  <tr>
                    <td>{{ $category->name }}</td>
                    <td>
                      @if ($category->media && $category->media->hasImage())
                        <img src="{{ $category->media->image_url }}" width="40" height="40"
                          class="rounded-2" style="object-fit:cover;" alt="">
                      @else
                        <span class="text-muted small">None</span>
                      @endif
                    </td>
                    <td>
                      @if ($category->media && $category->media->hasPdfMenu())
                        <a href="{{ $category->media->pdf_menu_url }}" target="_blank"
                          class="badge bg-danger bg-opacity-10 text-danger text-decoration-none">
                          <i class="material-icons-outlined align-middle" style="font-size:14px;">picture_as_pdf</i> View
                        </a>
                      @else
                        <span class="text-muted small">None</span>
                      @endif
                    </td>
                    <td class="text-end">
                      <button class="btn btn-sm btn-outline-primary" wire:click="manage({{ $category->id }})">
                        Manage
                      </button>
                      @if ($category->media && ($category->media->hasImage() || $category->media->hasPdfMenu()))
                        <button class="btn btn-sm btn-outline-danger"
                          wire:click="clearMedia({{ $category->id }})"
                          wire:confirm="Remove all media for '{{ $category->name }}'?">
                          Clear
                        </button>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center py-4 text-muted">
                      No categories yet — create some from the Categories page first.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- Manage form --}}
    @if ($showForm)
      <div class="col-lg-5">
        <div class="card rounded-4">
          <div class="card-body">
            <h6 class="fw-bold mb-3">
              Manage Media — {{ optional(\App\Models\Category::find($category_id))->name }}
            </h6>

            {{-- Banner image --}}
            <div class="mb-3">
              <label class="form-label">Banner Image</label>

              @if ($existing_image_path && ! $image)
                <div class="d-flex align-items-center gap-3 mb-2">
                  <img src="{{ asset('storage/'.$existing_image_path) }}" class="rounded-3" width="72" height="72" style="object-fit:cover;">
                  <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeImage">Remove Image</button>
                </div>
              @endif

              @if ($image)
                <div class="mb-2">
                  <img src="{{ $image->temporaryUrl() }}" class="rounded-3" width="72" height="72" style="object-fit:cover;">
                  <span class="text-muted small ms-2">New image selected — save to apply</span>
                </div>
              @endif

              <input type="file" wire:model="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
              @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <p class="form-text mb-0">JPG or PNG, up to 2MB.</p>
              <div wire:loading wire:target="image" class="text-muted small">Uploading…</div>
            </div>

            {{-- PDF menu --}}
            <div class="mb-4">
              <label class="form-label">PDF Menu</label>

              @if ($existing_pdf_menu_path && ! $pdf_menu)
                <div class="d-flex align-items-center gap-3 mb-2">
                  <a href="{{ asset('storage/'.$existing_pdf_menu_path) }}" target="_blank" class="text-decoration-none">
                    <i class="material-icons-outlined align-middle text-danger">picture_as_pdf</i>
                    {{ basename($existing_pdf_menu_path) }}
                  </a>
                  <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removePdfMenu">Remove PDF</button>
                </div>
              @endif

              @if ($pdf_menu)
                <div class="mb-2 text-muted small">
                  <i class="material-icons-outlined align-middle text-danger">picture_as_pdf</i>
                  {{ $pdf_menu->getClientOriginalName() }} selected — save to apply
                </div>
              @endif

              <input type="file" wire:model="pdf_menu" accept="application/pdf" class="form-control @error('pdf_menu') is-invalid @enderror">
              @error('pdf_menu') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <p class="form-text mb-0">PDF only, up to 10MB.</p>
              <div wire:loading wire:target="pdf_menu" class="text-muted small">Uploading…</div>
            </div>

            <div class="d-flex gap-2">
              <button type="button" class="btn btn-grd-primary px-4" wire:click="save">Save</button>
              <button type="button" class="btn btn-outline-secondary px-4" wire:click="cancel">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    @endif

  </div>
</div>