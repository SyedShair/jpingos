<div>

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Loyalty</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0)"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Reward coupons</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session('status-message'))
        <div class="alert alert-success" wire:key="status-flash-{{ now()->timestamp }}">
            {{ session('status-message') }}
        </div>
    @endif

    <p class="text-muted small mb-3">
        A customer earns one single-use coupon for every {{ $rule }} completed orders, matched by email address.
        The coupon is emailed automatically, and the next one needs {{ $rule }} new orders.
    </p>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-4">
            <div class="card h-100"><div class="card-body">
                <p class="text-muted small mb-1">Coupons issued</p>
                <h3 class="fw-bold mb-0">{{ number_format($issuedCount) }}</h3>
            </div></div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="card h-100"><div class="card-body">
                <p class="text-muted small mb-1">Waiting to be used</p>
                <h3 class="fw-bold mb-0 text-success">{{ number_format($activeCount) }}</h3>
            </div></div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card h-100"><div class="card-body">
                <p class="text-muted small mb-1">Redeemed</p>
                <h3 class="fw-bold mb-0">{{ number_format($usedCount) }}</h3>
            </div></div>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
        <span class="text-muted small me-1">Show:</span>
        @foreach (['' => 'All', 'active' => 'Active', 'used' => 'Used', 'expired' => 'Expired', 'voided' => 'Voided'] as $value => $label)
            <button type="button" class="btn btn-sm {{ $statusFilter === $value ? 'btn-dark' : 'btn-outline-secondary' }}" wire:click="$set('statusFilter', '{{ $value }}')">{{ $label }}</button>
        @endforeach

        <button type="button" class="btn btn-sm btn-dark ms-auto" wire:click="create">
            <i class="material-icons-outlined" style="font-size:16px; vertical-align: text-bottom;">add</i> New coupon
        </button>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-auto flex-grow-1">
            <div class="position-relative">
                <input class="form-control px-5" type="search" placeholder="Search by code or email..." wire:model.live.debounce.400ms="search">
                <span class="material-icons-outlined position-absolute ms-3 translate-middle-y start-0 top-50 fs-5">search</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive white-space-nowrap">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Customer email</th>
                            <th>Reward</th>
                            <th>Status</th>
                            <th>Issued</th>
                            <th>Expires / used</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $statusClasses = [
                                'active'  => 'bg-success-subtle text-success border-success-subtle',
                                'used'    => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                                'expired' => 'bg-warning-subtle text-warning border-warning-subtle',
                                'voided'  => 'bg-danger-subtle text-danger border-danger-subtle',
                            ];
                        @endphp
                        @forelse ($coupons as $coupon)
                            @php $status = $coupon->status; @endphp
                            <tr wire:key="coupon-{{ $coupon->id }}">
                                <td class="fw-semibold" style="letter-spacing:1px;">{{ $coupon->code }}</td>
                                <td>{{ $coupon->email }}</td>
                                <td>{{ $coupon->discount_label }}</td>
                                <td>
                                    <span class="lable-table {{ $statusClasses[$status] }} rounded border font-text2 fw-bold text-capitalize">{{ $status }}</span>
                                    @if (! $coupon->emailed_at)
                                        <br><span class="text-danger small">Email not sent</span>
                                    @endif
                                </td>
                                <td><span class="text-muted small">{{ $coupon->created_at->format('d M Y, g:i A') }}</span></td>
                                <td>
                                    @if ($status === 'used')
                                        <span class="text-muted small">
                                            {{ $coupon->used_at->format('d M Y') }}
                                            @if ($coupon->usedOrder) &middot; {{ $coupon->usedOrder->order_number }} @endif
                                        </span>
                                    @else
                                        <span class="text-muted small">{{ $coupon->expires_at->format('d M Y') }}</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    @if ($status === 'used')
                                        <span class="text-muted small">Kept as a record</span>
                                    @else
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-primary" wire:click="edit({{ $coupon->id }})" title="Edit">
                                            <i class="material-icons-outlined" style="font-size:16px;">edit</i>
                                        </button>

                                        @if ($status === 'active')
                                            <button type="button" class="btn btn-sm btn-icon btn-outline-secondary" wire:click="resend({{ $coupon->id }})" wire:loading.attr="disabled" title="Resend email">
                                                <i class="material-icons-outlined" style="font-size:16px;">mail</i>
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-icon btn-outline-warning"
                                                wire:click="void({{ $coupon->id }})"
                                                wire:confirm="Void {{ $coupon->code }}? The customer will no longer be able to use it (you can restore it later)."
                                                title="Void"
                                            >
                                                <i class="material-icons-outlined" style="font-size:16px;">block</i>
                                            </button>
                                        @endif

                                        @if ($status === 'voided')
                                            <button type="button" class="btn btn-sm btn-icon btn-outline-success" wire:click="restore({{ $coupon->id }})" title="Restore">
                                                <i class="material-icons-outlined" style="font-size:16px;">restore</i>
                                            </button>
                                        @endif

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-icon btn-outline-danger"
                                            wire:click="delete({{ $coupon->id }})"
                                            wire:confirm="Delete {{ $coupon->code }} permanently? This can't be undone."
                                            title="Delete"
                                        >
                                            <i class="material-icons-outlined" style="font-size:16px;">delete</i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    {{ $search !== '' || $statusFilter !== '' ? 'No coupons match those filters.' : 'No coupons yet — the first one is issued when a customer reaches ' . $rule . ' completed orders.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $coupons->links() }}
            </div>
        </div>
    </div>

    {{-- =================================================
         CREATE / EDIT MODAL
         Livewire-driven (not Bootstrap's JS modal), same approach as the
         order detail modal, so it stays in sync with component state.
    ================================================== --}}

    @if ($showForm)
        <div class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,.5);" wire:key="coupon-form-modal">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <form wire:submit="save">

                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">{{ $editingId ? 'Edit coupon' : 'New coupon' }}</h5>
                            <button type="button" class="btn-close" wire:click="closeForm"></button>
                        </div>

                        <div class="modal-body">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Code</label>
                                @if ($editingId)
                                    <input type="text" class="form-control" value="{{ $editingCode }}" disabled style="letter-spacing:1px;">
                                    <div class="form-text">A code can't be changed once it exists — it may already be in the customer's inbox.</div>
                                @else
                                    <input type="text" class="form-control" wire:model="code" placeholder="Leave blank to generate one" style="text-transform:uppercase; letter-spacing:1px;" maxlength="20">
                                    @error('code') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Customer email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" wire:model="email" placeholder="customer@example.com">
                                <div class="form-text">The coupon can only be used with this email address at checkout.</div>
                                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Discount type</label>
                                    <select class="form-select" wire:model.live="discount_type">
                                        <option value="percent">Percentage off</option>
                                        <option value="fixed">Fixed amount off</option>
                                    </select>
                                    @error('discount_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        @if ($discount_type === 'fixed') <span class="input-group-text">£</span> @endif
                                        <input type="number" step="0.01" min="0" class="form-control" wire:model="discount_value">
                                        @if ($discount_type === 'percent') <span class="input-group-text">%</span> @endif
                                    </div>
                                    @error('discount_value') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="form-text mb-3 mt-n2">Taken off the food total only — the delivery fee is never discounted.</div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Valid until <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" wire:model="expires_at" min="{{ now()->format('Y-m-d') }}">
                                @error('expires_at') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="coupon-send-email" wire:model="send_email">
                                <label class="form-check-label" for="coupon-send-email">
                                    {{ $editingId ? 'Email the updated coupon to the customer' : 'Email the coupon to the customer now' }}
                                </label>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeForm">Cancel</button>
                            <button type="submit" class="btn btn-dark" wire:loading.attr="disabled" wire:target="save">
                                {{ $editingId ? 'Save changes' : 'Create coupon' }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    @endif

</div>