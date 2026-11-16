<div>

    {{-- =================================================
         PAGE HEADER
    ================================================== --}}

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Newsletter</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0)"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Subscribers</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session('status-message'))
        <div class="alert alert-success" wire:key="status-flash-{{ now()->timestamp }}">
            {{ session('status-message') }}
        </div>
    @endif

    {{-- =================================================
         SUMMARY CARDS
    ================================================== --}}

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <p class="text-muted small mb-1">Total subscribers</p>
                <h3 class="fw-bold mb-0">{{ number_format($totalCount) }}</h3>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <p class="text-muted small mb-1">Active</p>
                <h3 class="fw-bold mb-0 text-success">{{ number_format($activeCount) }}</h3>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <p class="text-muted small mb-1">Unsubscribed</p>
                <h3 class="fw-bold mb-0">{{ number_format($unsubscribedCount) }}</h3>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <p class="text-muted small mb-1">New in the last 7 days</p>
                <h3 class="fw-bold mb-0">{{ number_format($newThisWeek) }}</h3>
            </div></div>
        </div>
    </div>

    {{-- =================================================
         FILTERS + SEARCH + EXPORT
    ================================================== --}}

    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
        <span class="text-muted small me-1">Show:</span>
        <button type="button" class="btn btn-sm {{ $statusFilter === '' ? 'btn-dark' : 'btn-outline-secondary' }}" wire:click="$set('statusFilter', '')">All</button>
        <button type="button" class="btn btn-sm {{ $statusFilter === 'active' ? 'btn-dark' : 'btn-outline-secondary' }}" wire:click="$set('statusFilter', 'active')">Active</button>
        <button type="button" class="btn btn-sm {{ $statusFilter === 'unsubscribed' ? 'btn-dark' : 'btn-outline-secondary' }}" wire:click="$set('statusFilter', 'unsubscribed')">Unsubscribed</button>

        <button type="button" class="btn btn-sm btn-outline-dark ms-auto" wire:click="export" wire:loading.attr="disabled" wire:target="export">
            <i class="material-icons-outlined" style="font-size:16px; vertical-align: text-bottom;">download</i> Export CSV
        </button>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-auto flex-grow-1">
            <div class="position-relative">
                <input
                    class="form-control px-5"
                    type="search"
                    placeholder="Search by email..."
                    wire:model.live.debounce.400ms="search"
                >
                <span class="material-icons-outlined position-absolute ms-3 translate-middle-y start-0 top-50 fs-5">search</span>
            </div>
        </div>
    </div>

    {{-- =================================================
         TABLE
    ================================================== --}}

    <div class="card">
        <div class="card-body">

            <div class="table-responsive white-space-nowrap">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Subscribed</th>
                            <th>Source</th>
                            <th>IP address</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subscribers as $subscriber)
                            <tr wire:key="subscriber-{{ $subscriber->id }}">
                                <td class="fw-semibold">{{ $subscriber->email }}</td>
                                <td>
                                    @if ($subscriber->is_active)
                                        <span class="lable-table bg-success-subtle text-success border-success-subtle rounded border font-text2 fw-bold">Active</span>
                                    @else
                                        <span class="lable-table bg-secondary-subtle text-secondary border-secondary-subtle rounded border font-text2 fw-bold">Unsubscribed</span>
                                        @if ($subscriber->unsubscribed_at)
                                            <br><span class="text-muted small">{{ $subscriber->unsubscribed_at->format('d M Y') }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $subscriber->subscribed_at?->format('d M Y, g:i A') ?? '—' }}</span>
                                </td>
                                <td class="text-capitalize">{{ $subscriber->source }}</td>
                                <td><span class="text-muted small">{{ $subscriber->ip_address ?? '—' }}</span></td>
                                <td class="text-end">
                                    @if ($subscriber->is_active)
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary"
                                            wire:click="unsubscribe({{ $subscriber->id }})"
                                            wire:confirm="Unsubscribe {{ $subscriber->email }}?"
                                            title="Take off the mailing list"
                                        >
                                            Unsubscribe
                                        </button>
                                    @endif
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-icon btn-outline-danger"
                                        wire:click="delete({{ $subscriber->id }})"
                                        wire:confirm="Delete {{ $subscriber->email }} permanently? This can't be undone."
                                        title="Delete"
                                    >
                                        <i class="material-icons-outlined" style="font-size: 16px;">delete</i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    {{ $search !== '' || $statusFilter !== '' ? 'No subscribers match those filters.' : 'No subscribers yet — they will appear here as people sign up from the website footer.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $subscribers->links() }}
            </div>

        </div>
    </div>

</div>