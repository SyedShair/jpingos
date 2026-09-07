<div>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="row g-4">

    {{-- List: top-level categories (Food Menu, Drink Menu, Dessert Menu...) with children nested --}}
    <div class="col-lg-{{ $showForm ? 7 : 12 }}">
      <div class="card rounded-4">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="mb-0">Categories</h5>
            <button type="button" class="btn btn-grd-primary" wire:click="openCreate">
              <i class="material-icons-outlined align-middle me-1" style="font-size:18px;">add</i>Add Main Category
            </button>
          </div>

          @forelse ($topLevel as $main)
            <div class="border rounded-4 p-3 mb-3">
              <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                  @if ($main->icon)
                    <span class="material-icons-outlined text-primary">{{ $main->icon }}</span>
                  @endif
                  <div>
                    <h6 class="mb-0 fw-bold">{{ $main->name }}</h6>
                    @if ($main->description)
                      <p class="mb-0 small text-muted">{{ $main->description }}</p>
                    @endif
                  </div>
                  <span class="badge bg-primary bg-opacity-10 text-primary ms-2">{{ $main->menu_items_count }} dishes</span>
                  @if (! $main->is_active)
                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Hidden</span>
                  @endif
                </div>
                <div class="d-flex align-items-center gap-2">
                  <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch"
                      wire:click="toggleActive({{ $main->id }})" @checked($main->is_active)>
                  </div>
                  <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $main->id }})">
                    <i class="material-icons-outlined fs-6 align-middle">edit</i>
                  </button>
                  <button class="btn btn-sm btn-outline-secondary" wire:click="openCreate({{ $main->id }})">
                    <i class="material-icons-outlined fs-6 align-middle">add</i> Sub
                  </button>
                  <button class="btn btn-sm btn-outline-danger"
                    wire:click="confirmDelete({{ $main->id }}, '{{ addslashes($main->name) }}', {{ $main->menu_items_count }}, {{ $main->children->count() }})">
                    <i class="material-icons-outlined fs-6 align-middle">delete</i>
                  </button>
                </div>
              </div>

              @if ($main->children->isNotEmpty())
                <div class="ms-4 mt-3 d-flex flex-column gap-2">
                  @foreach ($main->children as $child)
                    <div class="d-flex align-items-center justify-content-between border rounded-3 p-2">
                      <div class="d-flex align-items-center gap-2">
                        <span class="material-icons-outlined fs-6 text-muted">subdirectory_arrow_right</span>
                        @if ($child->icon)
                          <span class="material-icons-outlined fs-6 text-primary">{{ $child->icon }}</span>
                        @endif
                        <span>{{ $child->name }}</span>
                        <span class="badge bg-light text-dark border">{{ $child->menu_items_count }} dishes</span>
                        @if (! $child->is_active)
                          <span class="badge bg-secondary bg-opacity-10 text-secondary">Hidden</span>
                        @endif
                      </div>
                      <div class="d-flex align-items-center gap-2">
                        <div class="form-check form-switch mb-0">
                          <input class="form-check-input" type="checkbox" role="switch"
                            wire:click="toggleActive({{ $child->id }})" @checked($child->is_active)>
                        </div>
                        <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $child->id }})">
                          <i class="material-icons-outlined fs-6 align-middle">edit</i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger"
                          wire:click="confirmDelete({{ $child->id }}, '{{ addslashes($child->name) }}', {{ $child->menu_items_count }}, 0)">
                          <i class="material-icons-outlined fs-6 align-middle">delete</i>
                        </button>
                      </div>
                    </div>
                  @endforeach
                </div>
              @endif
            </div>
          @empty
            <p class="text-center text-muted py-4 mb-0">
              No categories yet. Start with a main category like "Food Menu" or "Drink Menu".
            </p>
          @endforelse

        </div>
      </div>
    </div>

    {{-- Inline form --}}
    @if ($showForm)
      <div class="col-lg-5">
        <div class="card rounded-4">
          <div class="card-body">
            <h6 class="fw-bold mb-3">{{ $editingId ? 'Edit Category' : 'New Category' }}</h6>

            <div class="mb-3">
              <label class="form-label">Belongs Under <span class="text-muted">(leave blank for a main category)</span></label>
              <select wire:model="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                <option value="">— Top-level (Main Category) —</option>
                @foreach ($parentOptions as $option)
                  @if ($option->id !== $editingId)
                    <option value="{{ $option->id }}">{{ $option->name }}</option>
                  @endif
                @endforeach
              </select>
              @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Name</label>
              <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror"
                placeholder="e.g. Food Menu, or Mains">
              @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Icon <span class="text-muted">(optional)</span></label>
              <input type="text" wire:model="icon" class="form-control" placeholder="e.g. restaurant, local_bar, icecream">
            </div>

            <div class="mb-3">
              <label class="form-label">Description <span class="text-muted">(optional)</span></label>
              <input type="text" wire:model="description" class="form-control">
            </div>

            <div class="mb-3">
              <label class="form-label">Display Order</label>
              <input type="number" wire:model="sort_order" min="0" class="form-control">
            </div>

            <div class="form-check form-switch mb-4">
              <input class="form-check-input" type="checkbox" wire:model="is_active" id="cat_is_active">
              <label class="form-check-label" for="cat_is_active">Active (visible on the menu)</label>
            </div>

            <div class="d-flex gap-2">
              <button type="button" class="btn btn-grd-primary px-4" wire:click="save">
                {{ $editingId ? 'Save Changes' : 'Create Category' }}
              </button>
              <button type="button" class="btn btn-outline-secondary px-4" wire:click="cancel">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    @endif

  </div>

  {{-- ================= Delete Confirmation Modal ================= --}}
  {{--
      $confirmingDeleteId etc. are set by confirmDelete() in the Livewire
      component. Rendered only while a delete is being confirmed, driven as
      a plain Bootstrap modal (no wire:confirm) so it can show the
      dish-count / sub-category warning and so "Cancel" calls back into the
      component instead of just closing a native browser dialog.
  --}}
  @if ($confirmingDeleteId)
    <div class="modal fade show" tabindex="-1" style="display:block;" role="dialog" wire:ignore.self>
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content rounded-4">
          <div class="modal-header">
            <h5 class="modal-title">Delete Category</h5>
            <button type="button" class="btn-close" wire:click="cancelDelete" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="mb-2">
              Are you sure you want to delete
              <strong>{{ $confirmingDeleteName }}</strong>?
            </p>
            @if ($confirmingDeleteDishCount > 0 || $confirmingDeleteChildCount > 0)
              <ul class="text-muted small mb-0">
                @if ($confirmingDeleteDishCount > 0)
                  <li>{{ $confirmingDeleteDishCount }} dish(es) will become uncategorized.</li>
                @endif
                @if ($confirmingDeleteChildCount > 0)
                  <li>{{ $confirmingDeleteChildCount }} sub-category(ies) will become top-level.</li>
                @endif
              </ul>
            @endif
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" wire:click="cancelDelete">Cancel</button>
            <button type="button" class="btn btn-danger" wire:click="deleteConfirmed" wire:loading.attr="disabled">
              <span wire:loading wire:target="deleteConfirmed" class="spinner-border spinner-border-sm me-1"></span>
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-backdrop fade show"></div>
  @endif

</div>