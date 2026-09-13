@php
    $steps = ['pending', 'confirmed', 'preparing', 'ready', 'completed'];
    $currentIndex = array_search($order->status, $steps);
    $stepLabels = [
        'pending'   => 'Pending',
        'confirmed' => 'Confirmed',
        'preparing' => 'Preparing',
        'ready'     => 'Ready',
        'completed' => 'Completed',
    ];
    $itemCount = $order->items->sum('quantity');
    $deliveryFee = $order->total - $order->subtotal;
@endphp

<style>
    .order-tracking-result { max-width: 960px; margin: 0 auto; }

    .order-tracking-result .summary-strip {
        display: flex;
        flex-wrap: wrap;
        border: 1px solid #e5e5e5;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 28px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, .04);
    }

    .order-tracking-result .summary-stat {
        flex: 1 1 0;
        min-width: 140px;
        padding: 18px 16px;
        text-align: center;
        border-right: 1px solid #eee;
    }

    .order-tracking-result .summary-stat:last-child {
        border-right: none;
    }

    .order-tracking-result .summary-stat .stat-label {
        display: block;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #9ca3af;
        margin-bottom: 6px;
    }

    .order-tracking-result .summary-stat .stat-value {
        display: block;
        font-size: 15px;
        font-weight: 700;
        color: #1A1A1A;
    }

    .order-tracking-result .summary-stat.stat-total .stat-value {
        color: #1a9c5c;
    }

    .order-tracking-result .status-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: capitalize;
    }

    .order-tracking-result .status-badge.cancelled { background: #fbe2e5; color: #b22b40; }
    .order-tracking-result .status-badge.active { background: #e6f6ee; color: #1a9c5c; }

    .order-tracking-result .tracking-status {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0 0 32px;
        counter-reset: step;
    }

    .order-tracking-result .tracking-status li {
        flex: 1;
        text-align: center;
        position: relative;
        color: #9ca3af;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .order-tracking-result .tracking-status li i {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        margin: 0 auto 8px;
        border-radius: 50%;
        background: #f0f0f0;
        color: #9ca3af;
        font-size: 14px;
    }

    .order-tracking-result .tracking-status li:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 16px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: #f0f0f0;
        z-index: -1;
    }

    .order-tracking-result .tracking-status li.active i {
        background: #1a9c5c;
        color: #fff;
    }

    .order-tracking-result .tracking-status li.active:not(:last-child)::after {
        background: #1a9c5c;
    }

    .order-tracking-result .tracking-status li.active { color: #1a9c5c; }

    .order-tracking-result .order-items-card {
        border: 1px solid #e5e5e5;
        border-radius: 12px;
        overflow: hidden;
    }

    .order-tracking-result table { width: 100%; border-collapse: collapse; }
    .order-tracking-result th {
        background: #fafafa;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #9ca3af;
    }
    .order-tracking-result th, .order-tracking-result td {
        padding: 12px 16px;
        border-bottom: 1px solid #e5e5e5;
        text-align: left;
        font-size: 14px;
    }

    .order-tracking-result .track-again {
        display: block;
        text-align: center;
        margin-top: 24px;
        font-size: 14px;
        color: #1a9c5c;
        font-weight: 600;
    }

    @media (max-width: 767px) {
        .order-tracking-result .summary-stat {
            flex: 0 0 50%;
            border-bottom: 1px solid #eee;
        }
        .order-tracking-result .summary-stat:nth-child(even) {
            border-right: none;
        }
    }
</style>

<div class="order-tracking-result">

    {{-- =========================================================
         SUMMARY STRIP — 6 stats in one row
    ========================================================== --}}
    <div class="summary-strip">

        <div class="summary-stat">
            <span class="stat-label">Order Number</span>
            <span class="stat-value">{{ $order->order_number }}</span>
        </div>

        <div class="summary-stat">
            <span class="stat-label">Placed</span>
            <span class="stat-value">{{ $order->created_at->format('d M Y') }}</span>
        </div>

        <div class="summary-stat">
            <span class="stat-label">Status</span>
            <span class="stat-value">
                @if ($order->status === 'cancelled')
                    <span class="status-badge cancelled">Cancelled</span>
                @else
                    <span class="status-badge active">{{ $stepLabels[$order->status] ?? ucfirst($order->status) }}</span>
                @endif
            </span>
        </div>

        <div class="summary-stat">
            <span class="stat-label">Items</span>
            <span class="stat-value">{{ $itemCount }}</span>
        </div>

        <div class="summary-stat">
            <span class="stat-label">Subtotal</span>
            <span class="stat-value">£{{ number_format($order->subtotal, 2) }}</span>
        </div>

        <div class="summary-stat stat-total">
            <span class="stat-label">Total</span>
            <span class="stat-value">£{{ number_format($order->total, 2) }}</span>
        </div>

    </div>

    @if ($order->status === 'cancelled')

        <div class="alert alert-danger text-center mb-6">
            This order was cancelled. If you think this is a mistake, please contact us directly.
        </div>

    @else

        <ul class="tracking-status">
            @foreach ($steps as $index => $step)
                <li class="{{ $currentIndex !== false && $index <= $currentIndex ? 'active' : '' }}">
                    <i class="fa fa-check"></i>
                    <span>{{ $stepLabels[$step] }}</span>
                </li>
            @endforeach
        </ul>

    @endif

    <div class="order-items-card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Qty</th>
                        <th>Item</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->quantity }}</td>
                            <td>
                                {{ $item->name }}
                                @if ($item->options)
                                    <br>
                                    <span class="small text-muted">
                                        {{ collect($item->options)->pluck('name')->join(', ') }}
                                    </span>
                                @endif
                            </td>
                            <td>£{{ number_format($item->unit_price, 2) }}</td>
                            <td>£{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @endforeach

                    <tr>
                        <td colspan="3" class="fw-bold text-end">Subtotal</td>
                        <td class="fw-bold">£{{ number_format($order->subtotal, 2) }}</td>
                    </tr>

                    @if ($order->total > $order->subtotal)
                        <tr>
                            <td colspan="3" class="fw-bold text-end">Delivery Fee</td>
                            <td class="fw-bold">£{{ number_format($deliveryFee, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="fw-bold text-end">Total</td>
                        <td class="fw-bold" style="color:#1a9c5c;">£{{ number_format($order->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <a href="javascript:void(0)" class="track-again" id="track-another-order">
        Track a different order
    </a>

</div>