<div wire:poll.10s>

    {{-- =================================================
         PAGE HEADER
    ================================================== --}}

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Orders</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0)"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Orders</li>
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
         QUICK STATUS TABS
         (labels only — no counts, since the component
         doesn't expose per-status totals)
    ================================================== --}}

    <div class="product-count d-flex align-items-center gap-3 gap-lg-4 mb-3 fw-medium flex-wrap font-text1">
        <a href="javascript:void(0)" wire:click="$set('statusFilter', '')" class="{{ $statusFilter === '' ? 'text-primary' : '' }}">
            All
        </a>
        @foreach ($statuses as $status)
            <a href="javascript:void(0)" wire:click="$set('statusFilter', '{{ $status }}')" class="text-capitalize {{ $statusFilter === $status ? 'text-primary' : '' }}">
                {{ $status }}
            </a>
        @endforeach
    </div>

    {{-- =================================================
         DATE FILTER
         Defaults to today on first load (see Manager::mount).
    ================================================== --}}

    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
        <span class="text-muted small me-1">Date:</span>

        <button type="button" class="btn btn-sm {{ $isToday ? 'btn-dark' : 'btn-outline-secondary' }}" wire:click="showToday">
            Today
        </button>

        <button type="button" class="btn btn-sm {{ $dateFilter === '' ? 'btn-dark' : 'btn-outline-secondary' }}" wire:click="showAllDates">
            All dates
        </button>

        <input
            type="date"
            class="form-control form-control-sm w-auto"
            wire:model.live="dateFilter"
            aria-label="Filter orders by date"
        >

        @if ($dateFilter !== '')
            <span class="text-muted small">
                Showing {{ \Illuminate\Support\Carbon::parse($dateFilter)->format('l, j F Y') }}
            </span>
        @else
            <span class="text-muted small">Showing all dates</span>
        @endif
    </div>

    {{-- =================================================
         FULFILMENT TYPE FILTER
    ================================================== --}}

    <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
        <span class="text-muted small me-1">Type:</span>
        <button type="button" class="btn btn-sm {{ $typeFilter === '' ? 'btn-dark' : 'btn-outline-secondary' }}" wire:click="$set('typeFilter', '')">
            All
        </button>
        @foreach ($orderTypes as $type)
            <button
                type="button"
                class="btn btn-sm text-capitalize {{ $typeFilter === $type ? 'btn-dark' : 'btn-outline-secondary' }}"
                wire:click="$set('typeFilter', '{{ $type }}')"
            >
                {{ $type }}
            </button>
        @endforeach
    </div>

    {{-- =================================================
         SEARCH
    ================================================== --}}

    <div class="row g-3 mb-3">
        <div class="col-auto flex-grow-1">
            <div class="position-relative">
                <input
                    class="form-control px-5"
                    type="search"
                    placeholder="Search by order number, name, email, or phone..."
                    wire:model.live.debounce.400ms="search"
                >
                <span class="material-icons-outlined position-absolute ms-3 translate-middle-y start-0 top-50 fs-5">search</span>
            </div>
        </div>
    </div>

    {{-- =================================================
         TABLE CARD
    ================================================== --}}

    <div class="card mt-4">
        <div class="card-body">

            <div class="customer-table">
                <div class="table-responsive white-space-nowrap">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Order Id</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Delivery Fee</th>
                                <th>Total</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr wire:key="order-row-{{ $order->id }}">
                                    <td>
                                        <a href="javascript:void(0)" wire:click="viewOrder({{ $order->id }})" class="fw-semibold">
                                            {{ $order->order_number }}
                                        </a>
                                        @if ($order->is_pre_order)
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle ms-1">Pre-order</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="customer-pic d-flex align-items-center justify-content-center rounded-circle bg-light-primary text-primary fw-bold" style="width:40px;height:40px;">
                                                {{ strtoupper(substr($order->first_name, 0, 1) . substr($order->last_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="mb-0 customer-name fw-bold">{{ $order->first_name }} {{ $order->last_name }}</p>
                                                <span class="text-muted small">{{ $order->email }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $typeClasses = [
                                                'delivery' => 'bg-info-subtle text-info border-info-subtle',
                                                'pickup'   => 'bg-warning-subtle text-warning border-warning-subtle',
                                                'dining'   => 'bg-primary-subtle text-primary border-primary-subtle',
                                            ];
                                        @endphp
                                        <span class="lable-table {{ $typeClasses[$order->order_type] ?? 'bg-secondary-subtle text-secondary border-secondary-subtle' }} rounded border font-text2 fw-bold text-capitalize">
                                            {{ $order->order_type }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($order->order_type === 'delivery')
                                            {{ $order->delivery_fee > 0 ? '£' . number_format($order->delivery_fee, 2) : 'Free' }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">£{{ number_format($order->total, 2) }}</td>
                                    <td>
                                        @php
                                            $paymentStatusClasses = [
                                                'pending'   => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                                                'paid'      => 'bg-success-subtle text-success border-success-subtle',
                                                'failed'    => 'bg-danger-subtle text-danger border-danger-subtle',
                                                'refunded'  => 'bg-warning-subtle text-warning border-warning-subtle',
                                                'cancelled' => 'bg-dark-subtle text-dark border-dark-subtle',
                                            ];
                                            $paymentMethodLabels = [
                                                'cod'  => 'Cash on Delivery',
                                                'card' => 'Card',
                                            ];
                                        @endphp
                                        @if ($order->latestPayment)
                                            <div class="d-flex flex-column gap-1">
                                                <span class="small fw-semibold">
                                                    {{ $paymentMethodLabels[$order->latestPayment->method] ?? ucfirst($order->latestPayment->method) }}
                                                </span>
                                                <span class="lable-table {{ $paymentStatusClasses[$order->latestPayment->status] ?? 'bg-secondary-subtle text-secondary border-secondary-subtle' }} rounded border font-text2 fw-bold text-capitalize" style="width:fit-content;">
                                                    {{ $order->latestPayment->status }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-muted small">No payment record</span>
                                        @endif
                                    </td>
                                    <td style="min-width: 170px;">
                                        @php
                                            $statusColors = [
                                                'pending'   => 'secondary',
                                                'confirmed' => 'info',
                                                'preparing' => 'primary',
                                                'ready'     => 'warning',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                            ];
                                            $color = $statusColors[$order->status] ?? 'secondary';
                                        @endphp
                                        <select
                                            class="form-select form-select-sm border-{{ $color }}"
                                            wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                                        >
                                            @foreach ($statuses as $status)
                                                <option value="{{ $status }}" @selected($order->status === $status)>
                                                    {{ ucfirst($status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $order->created_at->format('d M, g:i A') }}</span>
                                        @if ($order->is_pre_order && $order->pre_order_date)
                                            <br>
                                            <span class="text-warning small">
                                                For {{ \Illuminate\Support\Carbon::parse($order->pre_order_date)->format('d M') }}@if ($order->pre_order_time), {{ \Illuminate\Support\Carbon::parse($order->pre_order_time)->format('g:i A') }}@endif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-icon btn-outline-primary"
                                            wire:click="viewOrder({{ $order->id }})"
                                            title="View order"
                                        >
                                            <i class="material-icons-outlined" style="font-size: 16px;">visibility</i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">
                                        @if ($dateFilter !== '')
                                            No orders on {{ \Illuminate\Support\Carbon::parse($dateFilter)->format('j F Y') }}.
                                            <a href="javascript:void(0)" wire:click="showAllDates">View all dates</a>
                                        @else
                                            No orders found.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>

        </div>
    </div>

    {{-- =================================================
         ORDER DETAIL MODAL
         Visibility is Livewire-driven ($selectedOrder), not
         Bootstrap's JS modal API — deliberately, so it stays in
         sync with component state even across wire:poll refreshes.
    ================================================== --}}

    @if ($selectedOrder)
        @php
            $isDelivery = $selectedOrder->order_type === 'delivery';
            $isPickup   = $selectedOrder->order_type === 'pickup';
            $isDining   = $selectedOrder->order_type === 'dining';

            $typeLabel = $isDining ? 'Dining' : ucfirst($selectedOrder->order_type);

            $paymentMethodLabels = [
                'cod'  => 'Cash on Delivery',
                'card' => 'Card',
            ];
        @endphp

        <div class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,.5);" wire:key="order-detail-{{ $selectedOrder->id }}">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">

                    <div class="modal-header">
                        <div>
                            <h4 class="fw-bold mb-0">Order {{ $selectedOrder->order_number }}</h4>
                            <p class="mb-0 text-muted small">Placed {{ $selectedOrder->created_at->format('d M Y, g:i A') }}</p>
                        </div>
                        <button type="button" class="btn-close" wire:click="closeDetail"></button>
                    </div>

                    <div class="modal-body">

                        @if ($selectedOrder->is_pre_order && $selectedOrder->pre_order_date)
                            <div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
                                <i class="bi bi-calendar-event"></i>
                                <span>
                                    <strong>{{ $isDining ? 'Table booked for' : 'Pre-order scheduled for' }}</strong>
                                    {{ \Illuminate\Support\Carbon::parse($selectedOrder->pre_order_date)->format('l, j F Y') }}@if ($selectedOrder->pre_order_time) at {{ \Illuminate\Support\Carbon::parse($selectedOrder->pre_order_time)->format('g:i A') }}@endif
                                </span>
                            </div>
                        @endif

                        <div class="row">

                            {{-- ITEMS --}}
                            <div class="col-12 col-lg-8 d-flex mb-3 mb-lg-0">
                                <div class="card w-100 mb-0">
                                    <div class="card-body">
                                        <h5 class="mb-3 fw-bold">Items</h5>
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Qty</th>
                                                        <th>Item</th>
                                                        <th>Unit Price</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($selectedOrder->items as $item)
                                                        <tr>
                                                            <td>{{ $item->quantity }}</td>
                                                            <td>
                                                                {{ $item->name }}
                                                                @if ($item->options)
                                                                    <br>
                                                                    <span class="text-muted small">
                                                                        {{ collect($item->options)->pluck('name')->join(', ') }}
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td>£{{ number_format($item->unit_price, 2) }}</td>
                                                            <td>£{{ number_format($item->line_total, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SUMMARY + STATUS + PAYMENT --}}
                            <div class="col-12 col-lg-4 d-flex">
                                <div class="w-100">

                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title mb-3 fw-bold">Summary</h5>

                                            <div class="d-flex justify-content-between">
                                                <p class="mb-2 text-muted">Subtotal</p>
                                                <p class="mb-2">£{{ number_format($selectedOrder->subtotal, 2) }}</p>
                                            </div>

                                            @if ($isDelivery)
                                                <div class="d-flex justify-content-between">
                                                    <p class="mb-2 text-muted">Delivery fee</p>
                                                    <p class="mb-2">
                                                        {{ $selectedOrder->delivery_fee > 0
                                                            ? '£' . number_format($selectedOrder->delivery_fee, 2)
                                                            : 'Free' }}
                                                    </p>
                                                </div>
                                            @endif

                                            <div class="d-flex justify-content-between border-top pt-2">
                                                <p class="fw-semibold mb-0">Total</p>
                                                <p class="fw-bold mb-0">£{{ number_format($selectedOrder->total, 2) }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title mb-3 fw-bold">Order Status</h5>
                                            <select
                                                class="form-select"
                                                wire:change="updateStatus({{ $selectedOrder->id }}, $event.target.value)"
                                            >
                                                @foreach ($statuses as $status)
                                                    <option value="{{ $status }}" @selected($selectedOrder->status === $status)>
                                                        {{ ucfirst($status) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- =========================================================
                                         PAYMENT
                                         Read-only facts (method, gateway, transaction id) plus
                                         the one thing staff actually need to change: the status.
                                         Marking Cash on Delivery "Paid" here is how the till
                                         reconciles a driver's cash return against the order.
                                    ========================================================== --}}
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title mb-3 fw-bold">Payment</h5>

                                            @if ($selectedOrder->latestPayment)
                                                @php $payment = $selectedOrder->latestPayment; @endphp

                                                <div class="d-flex justify-content-between mb-2">
                                                    <p class="mb-0 text-muted">Method</p>
                                                    <p class="mb-0 fw-semibold">
                                                        {{ $paymentMethodLabels[$payment->method] ?? ucfirst($payment->method) }}
                                                    </p>
                                                </div>

                                                @if ($payment->gateway)
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <p class="mb-0 text-muted">Gateway</p>
                                                        <p class="mb-0 fw-semibold text-capitalize">{{ $payment->gateway }}</p>
                                                    </div>
                                                @endif

                                                @if ($payment->transaction_id)
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <p class="mb-0 text-muted">Transaction ID</p>
                                                        <p class="mb-0 fw-semibold text-truncate" style="max-width: 160px;" title="{{ $payment->transaction_id }}">
                                                            {{ $payment->transaction_id }}
                                                        </p>
                                                    </div>
                                                @endif

                                                @if ($payment->paid_at)
                                                    <div class="d-flex justify-content-between mb-3">
                                                        <p class="mb-0 text-muted">Paid at</p>
                                                        <p class="mb-0 fw-semibold">{{ $payment->paid_at->format('d M Y, g:i A') }}</p>
                                                    </div>
                                                @endif

                                                <label class="form-label text-muted small mb-1">Status</label>
                                                <select
                                                    class="form-select"
                                                    wire:change="updatePaymentStatus({{ $selectedOrder->id }}, $event.target.value)"
                                                >
                                                    @foreach ($paymentStatuses as $pStatus)
                                                        <option value="{{ $pStatus }}" @selected($payment->status === $pStatus)>
                                                            {{ ucfirst($pStatus) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <p class="text-muted mb-0">No payment record for this order.</p>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                        {{-- BILLING / DELIVERY DETAILS --}}
                        <h5 class="fw-bold mb-3 mt-2">Customer &amp; {{ $typeLabel }} Details</h5>
                        <div class="row g-3 row-cols-1 row-cols-md-2 row-cols-lg-3">

                            <div class="col">
                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                    <div class="detail-icon fs-5"><i class="bi bi-person-circle"></i></div>
                                    <div class="detail-info">
                                        <p class="fw-bold mb-1">Customer Name</p>
                                        <p class="mb-0">{{ $selectedOrder->first_name }} {{ $selectedOrder->last_name }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                    <div class="detail-icon fs-5"><i class="bi bi-envelope-fill"></i></div>
                                    <div class="detail-info">
                                        <p class="fw-bold mb-1">Email</p>
                                        <a href="mailto:{{ $selectedOrder->email }}" class="mb-0">{{ $selectedOrder->email }}</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                    <div class="detail-icon fs-5"><i class="bi bi-telephone-fill"></i></div>
                                    <div class="detail-info">
                                        <p class="fw-bold mb-1">Phone</p>
                                        <a href="tel:{{ $selectedOrder->phone }}" class="mb-0">{{ $selectedOrder->phone }}</a>
                                    </div>
                                </div>
                            </div>

                            @if ($isDelivery)
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5"><i class="bi bi-house-door-fill"></i></div>
                                        <div class="detail-info">
                                            <p class="fw-bold mb-1">Address</p>
                                            <p class="mb-0">
                                                {{ $selectedOrder->address }}
                                                @if ($selectedOrder->apartment), {{ $selectedOrder->apartment }}@endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5"><i class="bi bi-geo-alt-fill"></i></div>
                                        <div class="detail-info">
                                            <p class="fw-bold mb-1">City / Postcode</p>
                                            <p class="mb-0">
                                                {{ $selectedOrder->city }}
                                                @if ($selectedOrder->postcode) {{ $selectedOrder->postcode }} @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @elseif ($isPickup)
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5"><i class="bi bi-shop"></i></div>
                                        <div class="detail-info">
                                            <p class="fw-bold mb-1">Pickup</p>
                                            <p class="mb-0 text-muted">Collecting in store — no delivery address.</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5"><i class="bi bi-cup-hot-fill"></i></div>
                                        <div class="detail-info">
                                            <p class="fw-bold mb-1">Dining</p>
                                            <p class="mb-0 text-muted">Table reservation — no delivery address.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($selectedOrder->notes)
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5"><i class="bi bi-chat-left-text-fill"></i></div>
                                        <div class="detail-info">
                                            <p class="fw-bold mb-1">Note</p>
                                            <p class="mb-0 fst-italic">{{ $selectedOrder->notes }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>

                    </div>

                    <div class="modal-footer">
                        <div class="d-flex align-items-center gap-2 me-auto">
                            <select class="form-select form-select-sm w-auto" id="thermal-width-select">
                                <option value="80">80mm</option>
                                <option value="58">58mm</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-outline-dark" onclick="printOrderReceipt({{ $selectedOrder->id }})">
                                <i class="material-icons-outlined" style="font-size:16px; vertical-align: text-bottom;">receipt</i> Print Receipt
                            </button>
                            <a href="{{ route('orders.print', ['order' => $selectedOrder->id, 'format' => 'a4']) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                <i class="material-icons-outlined" style="font-size:16px; vertical-align: text-bottom;">description</i> Print A4
                            </a>
                        </div>
                        <button type="button" class="btn btn-secondary" wire:click="closeDetail">Close</button>
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>

<script>
    function printOrderReceipt(orderId) {
        const widthSelect = document.getElementById('thermal-width-select');
        const width = widthSelect ? widthSelect.value : 80;
        window.open(`/admin/orders/${orderId}/print?format=thermal&width=${width}`, '_blank');
    }
</script>