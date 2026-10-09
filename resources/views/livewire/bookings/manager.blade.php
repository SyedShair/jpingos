<div wire:poll.30s>

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Bookings</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bookings</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session('status-message'))
        <div class="alert alert-success">{{ session('status-message') }}</div>
    @endif

    <div class="d-flex align-items-center gap-3 mb-3 flex-wrap font-text1">
        <a href="javascript:void(0)" wire:click="$set('statusFilter', '')" class="{{ $statusFilter === '' ? 'text-primary fw-semibold' : '' }}">All</a>
        @foreach ($statuses as $status)
            <a href="javascript:void(0)" wire:click="$set('statusFilter', '{{ $status }}')" class="text-capitalize {{ $statusFilter === $status ? 'text-primary fw-semibold' : '' }}">{{ $status }}</a>
        @endforeach
    </div>

    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
        <span class="text-muted small me-1">Date:</span>
        <button type="button" class="btn btn-sm {{ $dateFilter === now()->toDateString() ? 'btn-dark' : 'btn-outline-secondary' }}" wire:click="showToday">Today</button>
        <button type="button" class="btn btn-sm {{ $dateFilter === '' ? 'btn-dark' : 'btn-outline-secondary' }}" wire:click="showAllDates">All dates</button>
        <input type="date" class="form-control form-control-sm w-auto" wire:model.live="dateFilter">
    </div>

    <div class="row g-3 mb-3">
        <div class="col-auto flex-grow-1">
            <div class="position-relative">
                <input class="form-control px-5" type="search" placeholder="Search by reference, name, email, phone or GuestPlan ID..." wire:model.live.debounce.400ms="search">
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
                            <th>Reference</th>
                            <th>Guest</th>
                            <th>Event</th>
                            <th>Date &amp; time</th>
                            <th>Party</th>
                            <th>GuestPlan</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr wire:key="booking-row-{{ $booking->id }}">
                                <td>
                                    <a href="javascript:void(0)" wire:click="view({{ $booking->id }})" class="fw-semibold">{{ $booking->booking_number }}</a>
                                </td>
                                <td>
                                    {{ $booking->customer_name }}
                                    <br><span class="text-muted small">{{ $booking->customer_email }}</span>
                                </td>
                                <td>{{ $booking->event->name ?? '—' }}</td>
                                <td>{{ $booking->booking_date->format('d M Y') }} · {{ \Illuminate\Support\Carbon::parse($booking->booking_time)->format('g:i A') }}</td>
                                <td>{{ $booking->party_size }}</td>
                                <td>
                                    @if ($booking->guestplan_booking_id)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $booking->guestplan_booking_id }}</span>
                                    @elseif ($booking->guestplan_error)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle" title="{{ $booking->guestplan_error }}">Not synced</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td style="min-width:150px;">
                                    @php
                                        $statusColors = ['pending' => 'secondary', 'confirmed' => 'success', 'failed' => 'danger', 'cancelled' => 'dark'];
                                        $color = $statusColors[$booking->status] ?? 'secondary';
                                    @endphp
                                    <select class="form-select form-select-sm border-{{ $color }}" wire:change="updateStatus({{ $booking->id }}, $event.target.value)">
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}" @selected($booking->status === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-primary" wire:click="view({{ $booking->id }})" title="View">
                                        <i class="material-icons-outlined" style="font-size:16px;">visibility</i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger" wire:click="delete({{ $booking->id }})"
                                        wire:confirm="Delete this booking? It stays in GuestPlan — this only removes it from your records here."
                                        title="Delete">
                                        <i class="material-icons-outlined" style="font-size:16px;">delete</i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-5">No bookings found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $bookings->links() }}</div>
        </div>
    </div>

    @if ($selectedBooking)
        <div class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,.5);" wire:key="detail-{{ $selectedBooking->id }}">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h4 class="fw-bold mb-0">{{ $selectedBooking->booking_number }}</h4>
                            <p class="mb-0 text-muted small">Placed {{ $selectedBooking->created_at->format('d M Y, g:i A') }}</p>
                        </div>
                        <button type="button" class="btn-close" wire:click="closeDetail"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-between mb-2"><span class="text-muted">Guest</span><span class="fw-semibold">{{ $selectedBooking->customer_name }}</span></div>
                        <div class="d-flex justify-content-between mb-2"><span class="text-muted">Email</span><a href="mailto:{{ $selectedBooking->customer_email }}">{{ $selectedBooking->customer_email }}</a></div>
                        <div class="d-flex justify-content-between mb-2"><span class="text-muted">Phone</span><a href="tel:{{ $selectedBooking->customer_phone }}">{{ $selectedBooking->customer_phone }}</a></div>
                        <div class="d-flex justify-content-between mb-2"><span class="text-muted">Event</span><span>{{ $selectedBooking->event->name ?? 'Regular booking' }}</span></div>
                        <div class="d-flex justify-content-between mb-2"><span class="text-muted">Date &amp; time</span><span>{{ $selectedBooking->booking_date->format('l, j F Y') }} · {{ \Illuminate\Support\Carbon::parse($selectedBooking->booking_time)->format('g:i A') }}</span></div>
                        <div class="d-flex justify-content-between mb-2"><span class="text-muted">Party size</span><span>{{ $selectedBooking->party_size }}</span></div>
                        @if ($selectedBooking->notes)
                            <div class="mb-2"><span class="text-muted d-block">Notes</span><span class="fst-italic">{{ $selectedBooking->notes }}</span></div>
                        @endif

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">GuestPlan</span>
                            @if ($selectedBooking->guestplan_booking_id)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Synced · {{ $selectedBooking->guestplan_booking_id }}</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Not synced</span>
                            @endif
                        </div>
                        @if ($selectedBooking->guestplan_error)
                            <div class="alert alert-danger small mb-2">{{ $selectedBooking->guestplan_error }}</div>
                        @endif
                        @unless ($selectedBooking->guestplan_booking_id)
                            <button type="button" class="btn btn-sm btn-outline-dark" wire:click="retrySync({{ $selectedBooking->id }})" wire:loading.attr="disabled">
                                Retry GuestPlan sync
                            </button>
                        @endunless
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDetail">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
