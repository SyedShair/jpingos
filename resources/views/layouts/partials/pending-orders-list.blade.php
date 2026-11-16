@forelse ($pendingOrders as $order)
    <div>
        <a class="dropdown-item border-bottom py-2" href="{{ route('orders.index') }}">
            <div class="d-flex align-items-center gap-3">
                <div class="user-wrapper {{ $loop->even ? 'bg-primary text-primary' : 'bg-warning text-warning' }} bg-opacity-10">
                    <span>{{ strtoupper(substr($order->first_name, 0, 1) . substr($order->last_name, 0, 1)) }}</span>
                </div>
                <div class="">
                    <h5 class="notify-title">{{ $order->order_number }} · £{{ number_format($order->total, 2) }}</h5>
                    <p class="mb-0 notify-desc">{{ $order->first_name }} {{ $order->last_name }} — {{ ucfirst($order->order_type) }}</p>
                    <p class="mb-0 notify-time">{{ $order->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </a>
    </div>
@empty
    <div class="px-3 py-4 text-center text-muted">
        No pending orders right now.
    </div>
@endforelse
