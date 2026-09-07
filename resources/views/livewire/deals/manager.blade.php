<div>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="row g-4">

    {{-- Grouped deal listing --}}
    <div class="col-lg-{{ $showForm ? 6 : 12 }}">

      <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="mb-0">Deals &amp; Offers</h5>
        <button type="button" class="btn btn-grd-primary" wire:click="openCreate">
          <i class="material-icons-outlined align-middle me-1" style="font-size:18px;">add</i>New Deal
        </button>
      </div>

      @forelse ($typeGroups as $groupName => $types)
        <div class="card rounded-4 mb-4">
          <div class="card-body">
            <h6 class="fw-bold mb-3">{{ $groupName }}</h6>

            @php $groupDeals = $dealGroups[$groupName] ?? collect(); @endphp

            @forelse ($groupDeals as $deal)
              <div class="d-flex align-items-center justify-content-between border rounded-3 p-3 mb-2">
                <div class="d-flex align-items-center gap-3">
                  <img src="{{ $deal->image_url }}" alt="{{ $deal->name }}"
                    class="rounded-3" style="width:64px;height:48px;object-fit:cover;">
                  <div>
                    <div class="d-flex align-items-center gap-2">
                      <span class="fw-semibold">{{ $deal->name }}</span>
                      <span class="badge bg-primary bg-opacity-10 text-primary">{{ \App\Models\Deal::typeLabel($deal->type) }}</span>
                      <span class="badge bg-{{ $deal->statusBadgeClass() }} bg-opacity-10 text-{{ $deal->statusBadgeClass() }}">
                        {{ $deal->statusLabel() }}
                      </span>
                    </div>
                    <p class="mb-0 small text-muted">{{ $deal->scheduleSummary() }}</p>
                    @if ($deal->promo_code)
                      <p class="mb-0 small font-monospace">Code: {{ $deal->promo_code }}</p>
                    @endif
                  </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch"
                      wire:click="toggleActive({{ $deal->id }})" @checked($deal->is_active)>
                  </div>
                  <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $deal->id }})">
                    <i class="material-icons-outlined fs-6 align-middle">edit</i>
                  </button>
                  <button class="btn btn-sm btn-outline-danger"
                    wire:click="delete({{ $deal->id }})"
                    wire:confirm="Delete '{{ $deal->name }}'?">
                    <i class="material-icons-outlined fs-6 align-middle">delete</i>
                  </button>
                </div>
              </div>
            @empty
              <p class="text-muted small mb-0">No deals in this group yet.</p>
            @endforelse
          </div>
        </div>
      @endforeach

    </div>

    {{-- Form --}}
    @if ($showForm)
      <div class="col-lg-6">
        <div class="card rounded-4">
          <div class="card-body">
            <h6 class="fw-bold mb-3">{{ $editingId ? 'Edit Deal' : 'New Deal' }}</h6>

            <div class="mb-3">
              <label class="form-label">Deal Type</label>
              <select wire:model.live="type" class="form-select" {{ $editingId ? 'disabled' : '' }}>
                @foreach ($typeGroups as $groupName => $types)
                  <optgroup label="{{ $groupName }}">
                    @foreach ($types as $value => $label)
                      <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                  </optgroup>
                @endforeach
              </select>
              @if ($editingId)
                <p class="form-text mb-0">Type can't be changed after creation — delete and recreate if needed.</p>
              @endif
            </div>

            <div class="mb-3">
              <label class="form-label">Deal Name</label>
              <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror"
                placeholder="e.g. Tuesday Burger Flash Sale">
              @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Description <span class="text-muted">(optional, shown to customers)</span></label>
              <textarea wire:model="description" rows="2" class="form-control"></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">
                Deal Image <span class="text-muted">(optional)</span>
              </label>

              @if ($photo)
                <div class="mb-2">
                  <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="rounded-3"
                    style="width:160px;height:100px;object-fit:cover;">
                </div>
              @elseif ($existingImage)
                <div class="mb-2">
                  <img src="{{ asset('storage/'.$existingImage) }}" alt="Current image" class="rounded-3"
                    style="width:160px;height:100px;object-fit:cover;">
                </div>
              @endif

              <input type="file" wire:model="photo" accept="image/*"
                class="form-control @error('photo') is-invalid @enderror">
              @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <div wire:loading wire:target="photo" class="form-text">Uploading…</div>
              @if (in_array($type, ['combo', 'bundle']))
                <p class="form-text mb-0">
                  Leave blank to automatically use the first bundle item's photo instead.
                </p>
              @else
                <p class="form-text mb-0">Max 2MB. Shown on the deal card if provided.</p>
              @endif
            </div>

            {{-- === Flash Deal / Happy Hour / Lunch Special === --}}
            @if (in_array($type, ['flash_deal', 'happy_hour', 'lunch_special']))
              <div class="row g-3 mb-3">
                <div class="col-6">
                  <label class="form-label">Discount Type</label>
                  <select wire:model="discount_type" class="form-select">
                    <option value="percentage">Percentage Off</option>
                    <option value="fixed_amount">Fixed Amount Off</option>
                  </select>
                </div>
                <div class="col-6">
                  <label class="form-label">{{ $discount_type === 'percentage' ? 'Percent Off (%)' : 'Amount Off ($)' }}</label>
                  <input type="number" step="0.01" wire:model="discount_value"
                    class="form-control @error('discount_value') is-invalid @enderror">
                  @error('discount_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Applies To Dishes</label>
                <input type="text" wire:model.live.debounce.300ms="itemSearch" class="form-control mb-2" placeholder="Search dishes...">
                <select wire:model="applyItemIds" multiple class="form-select @error('applyItemIds') is-invalid @enderror" size="5">
                  @foreach ($menuItems as $mi)
                    <option value="{{ $mi->id }}">{{ $mi->name }} (${{ number_format($mi->price, 2) }})</option>
                  @endforeach
                </select>
                @error('applyItemIds') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <p class="form-text mb-0">Ctrl/Cmd-click to select multiple.</p>
              </div>

              @if ($type === 'happy_hour' || $type === 'lunch_special')
                <div class="mb-3">
                  <label class="form-label d-block">Recurring Days</label>
                  <div class="d-flex flex-wrap gap-3">
                    @foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $i => $label)
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="{{ $i }}" wire:model="recurring_days" id="day{{ $i }}">
                        <label class="form-check-label" for="day{{ $i }}">{{ $label }}</label>
                      </div>
                    @endforeach
                  </div>
                </div>

                {{--
                  Plain text, 24-hour only, no native time picker at all.
                  This is the actual fix for the AM/PM confusion — a native
                  <input type="time"> renders in whatever format the OS is
                  set to (often 12-hour on Windows), no matter what HTML
                  attributes you add. A text field has no such concept.
                --}}
                <div class="row g-3 mb-1">
                  <div class="col-6">
                    <label class="form-label">Daily Start Time</label>
                    <input type="text" wire:model="daily_start_time" inputmode="numeric"
                      placeholder="16:00" maxlength="5"
                      class="form-control @error('daily_start_time') is-invalid @enderror">
                    @error('daily_start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                  <div class="col-6">
                    <label class="form-label">Daily End Time</label>
                    <input type="text" wire:model="daily_end_time" inputmode="numeric"
                      placeholder="18:00" maxlength="5"
                      class="form-control @error('daily_end_time') is-invalid @enderror">
                    @error('daily_end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                </div>
                <p class="form-text mb-3">
                  24-hour format, always. E.g. 4:00 PM → <strong>16:00</strong>, 6:00 PM → <strong>18:00</strong>,
                  midnight → <strong>00:00</strong>, noon → <strong>12:00</strong>.
                </p>
              @else
                <div class="row g-3 mb-1">
                  <div class="col-3">
                    <label class="form-label">Starts — Date</label>
                    <input type="date" wire:model="starts_at_date" class="form-control @error('starts_at_date') is-invalid @enderror">
                    @error('starts_at_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                  <div class="col-3">
                    <label class="form-label">Starts — Time</label>
                    <input type="text" wire:model="starts_at_time" inputmode="numeric" placeholder="00:00" maxlength="5"
                      class="form-control @error('starts_at_time') is-invalid @enderror">
                    @error('starts_at_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                  <div class="col-3">
                    <label class="form-label">Ends — Date</label>
                    <input type="date" wire:model="ends_at_date" class="form-control @error('ends_at_date') is-invalid @enderror">
                    @error('ends_at_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                  <div class="col-3">
                    <label class="form-label">Ends — Time</label>
                    <input type="text" wire:model="ends_at_time" inputmode="numeric" placeholder="23:59" maxlength="5"
                      class="form-control @error('ends_at_time') is-invalid @enderror">
                    @error('ends_at_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                </div>
                <p class="form-text mb-3">
                  Time is 24-hour, always (leave blank for start-of-day / end-of-day).
                  This deal turns itself off automatically once "Ends" passes.
                </p>
              @endif
            @endif

            {{-- === Tiered Spend === --}}
            @if ($type === 'tiered_spend')
              <div class="row g-3 mb-3">
                <div class="col-12">
                  <label class="form-label">Minimum Spend ($)</label>
                  <input type="number" step="0.01" wire:model="min_spend"
                    class="form-control @error('min_spend') is-invalid @enderror">
                  @error('min_spend') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-6">
                  <label class="form-label">Reward</label>
                  <select wire:model.live="discount_type" class="form-select">
                    <option value="fixed_amount">Fixed Amount Off</option>
                    <option value="free_delivery">Free Delivery</option>
                  </select>
                </div>
                @if ($discount_type === 'fixed_amount')
                  <div class="col-6">
                    <label class="form-label">Amount Off ($)</label>
                    <input type="number" step="0.01" wire:model="discount_value" class="form-control">
                  </div>
                @endif
              </div>
            @endif

            {{-- === BOGO === --}}
            @if ($type === 'bogo')
              <div class="mb-3">
                <label class="form-label">"Buy" Dish(es)</label>
                <input type="text" wire:model.live.debounce.300ms="itemSearch" class="form-control mb-2" placeholder="Search dishes...">
                <select wire:model="buyItemIds" multiple class="form-select @error('buyItemIds') is-invalid @enderror" size="4">
                  @foreach ($menuItems as $mi)
                    <option value="{{ $mi->id }}">{{ $mi->name }}</option>
                  @endforeach
                </select>
                @error('buyItemIds') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="mb-3">
                <label class="form-label">"Get" Dish(es) <span class="text-muted">(leave blank to mean "same item")</span></label>
                <select wire:model="freeItemIds" multiple class="form-select" size="4">
                  @foreach ($menuItems as $mi)
                    <option value="{{ $mi->id }}">{{ $mi->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-4">
                  <label class="form-label">Buy Qty</label>
                  <input type="number" min="1" wire:model="buy_quantity" class="form-control">
                </div>
                <div class="col-4">
                  <label class="form-label">Get Qty</label>
                  <input type="number" min="1" wire:model="get_quantity" class="form-control">
                </div>
                <div class="col-4">
                  <label class="form-label">Get Discount %</label>
                  <input type="number" min="1" max="100" wire:model="get_discount_percent" class="form-control">
                  <p class="form-text mb-0">100 = fully free</p>
                </div>
              </div>
            @endif

            {{-- === Free Gift === --}}
            @if ($type === 'free_gift')
              <div class="mb-3">
                <label class="form-label">Minimum Spend ($)</label>
                <input type="number" step="0.01" wire:model="min_spend"
                  class="form-control @error('min_spend') is-invalid @enderror">
                @error('min_spend') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="mb-3">
                <label class="form-label">Free Item</label>
                <input type="text" wire:model.live.debounce.300ms="itemSearch" class="form-control mb-2" placeholder="Search dishes...">
                <select wire:model="freeItemIds" multiple class="form-select @error('freeItemIds') is-invalid @enderror" size="4">
                  @foreach ($menuItems as $mi)
                    <option value="{{ $mi->id }}">{{ $mi->name }}</option>
                  @endforeach
                </select>
                @error('freeItemIds') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            @endif

            {{-- === Combo / Bundle === --}}
            @if (in_array($type, ['combo', 'bundle']))
              <div class="mb-3">
                <label class="form-label">Fixed Bundle Price ($)</label>
                <input type="number" step="0.01" wire:model="combo_price"
                  class="form-control @error('combo_price') is-invalid @enderror">
                @error('combo_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <label class="form-label d-block">Bundle Components</label>
              @error('bundleComponents') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

              @foreach ($bundleComponents as $index => $component)
                <div class="row g-2 align-items-center mb-2">
                  <div class="col-7">
                    <select wire:model="bundleComponents.{{ $index }}.menu_item_id" class="form-select form-select-sm">
                      <option value="">Select dish…</option>
                      @foreach ($menuItems as $mi)
                        <option value="{{ $mi->id }}">{{ $mi->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-3">
                    <input type="number" min="1" wire:model="bundleComponents.{{ $index }}.quantity"
                      class="form-control form-control-sm" placeholder="Qty">
                  </div>
                  <div class="col-2">
                    <button type="button" class="btn btn-sm btn-outline-danger w-100"
                      wire:click="removeBundleComponent({{ $index }})">✕</button>
                  </div>
                </div>
              @endforeach

              <button type="button" class="btn btn-sm btn-outline-primary mb-3" wire:click="addBundleComponent">
                + Add Item
              </button>

              <div class="mb-3">
                <input type="text" wire:model.live.debounce.300ms="itemSearch" class="form-control" placeholder="Search dishes to add above...">
              </div>
            @endif

            {{-- === Promo Code === --}}
            @if ($type === 'promo_code')
              <div class="mb-3">
                <label class="form-label">Promo Code</label>
                <input type="text" wire:model="promo_code" style="text-transform:uppercase"
                  class="form-control @error('promo_code') is-invalid @enderror" placeholder="e.g. WELCOME10">
                @error('promo_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="row g-3 mb-3">
                <div class="col-6">
                  <label class="form-label">Discount Type</label>
                  <select wire:model="discount_type" class="form-select">
                    <option value="percentage">Percentage Off</option>
                    <option value="fixed_amount">Fixed Amount Off</option>
                  </select>
                </div>
                <div class="col-6">
                  <label class="form-label">{{ $discount_type === 'percentage' ? 'Percent Off (%)' : 'Amount Off ($)' }}</label>
                  <input type="number" step="0.01" wire:model="discount_value" class="form-control">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Usage Limit <span class="text-muted">(optional)</span></label>
                <input type="number" min="1" wire:model="usage_limit" class="form-control" placeholder="e.g. 500 uses total">
              </div>
            @endif

            <div class="form-check form-switch mb-4">
              <input class="form-check-input" type="checkbox" wire:model="is_active" id="deal_is_active">
              <label class="form-check-label" for="deal_is_active">Active</label>
            </div>

            <div class="d-flex gap-2">
              <button type="button" class="btn btn-grd-primary px-4" wire:click="save" wire:loading.attr="disabled" wire:target="save,photo">
                {{ $editingId ? 'Save Changes' : 'Create Deal' }}
              </button>
              <button type="button" class="btn btn-outline-secondary px-4" wire:click="cancel">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    @endif

  </div>
</div>