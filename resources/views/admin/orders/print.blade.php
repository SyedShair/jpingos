<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order {{ $order->order_number }}</title>
    <style>
        @page {
            size: {{ $format === 'thermal' ? $width.'mm auto' : 'A4' }};
            margin: {{ $format === 'thermal' ? '0' : '15mm' }};
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: {{ $format === 'thermal' ? '11px' : '13px' }};
            color: #000;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }

        .invoice-box {
            width: {{ $format === 'thermal' ? $width.'mm' : '320px' }};
            border: 2px solid #000;
            padding: {{ $format === 'thermal' ? '3mm' : '16px' }};
            box-sizing: border-box;
        }

        .center { text-align: center; }
        .bold   { font-weight: bold; }

        .invoice-title {
            font-size: {{ $format === 'thermal' ? '14px' : '16px' }};
            margin: 0 0 2px;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            margin: 4px 0;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .section-title {
            text-align: center;
            letter-spacing: 2px;
            margin: 10px 0;
        }

        .schedule-box {
            border: 1px dashed #000;
            padding: 5px 8px;
            margin: 8px 0;
            text-align: center;
            font-weight: bold;
        }

        .address-block {
            margin: 8px 0;
            line-height: 1.45;
        }

        .address-block .label {
            font-weight: bold;
            letter-spacing: 1px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        table.items th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
            font-weight: bold;
        }

        table.items th.num,
        table.items td.num {
            text-align: right;
        }

        table.items td {
            padding: 3px 0;
            vertical-align: top;
        }

        .item-options {
            margin: 0 0 4px 10px;
            font-size: {{ $format === 'thermal' ? '9px' : '11px' }};
            color: #333;
        }

        .item-options .opt-line {
            display: flex;
            justify-content: space-between;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }

        .totals-row.grand {
            font-weight: bold;
            border-top: 1px dashed #000;
            margin-top: 4px;
            padding-top: 6px;
        }

        /* =====================================================
           PAYMENT
           Sits under the totals so the amount due and how it's
           being settled read together — matters most for COD,
           where the driver needs to know cash is still owed.
        ====================================================== */

        .payment-block {
            border: 1px dashed #000;
            padding: 6px 8px;
            margin: 10px 0 0;
        }

        .payment-block .payment-title {
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .payment-block .payment-row {
            display: flex;
            justify-content: space-between;
            padding: 1px 0;
        }

        .payment-due {
            text-align: center;
            font-weight: bold;
            margin-top: 6px;
            padding-top: 5px;
            border-top: 1px dashed #000;
        }

        .thank-you {
            text-align: center;
            font-style: italic;
            margin: 16px 0 12px;
        }

        .footer-info {
            text-align: center;
            font-size: {{ $format === 'thermal' ? '9px' : '11px' }};
            color: #333;
            margin-bottom: 10px;
        }

        .qr-wrap {
            text-align: center;
        }

        .qr-wrap img {
            width: {{ $format === 'thermal' ? '90px' : '110px' }};
            height: {{ $format === 'thermal' ? '90px' : '110px' }};
        }
    </style>
</head>
<body onload="window.print()">

@php
    $isDelivery = $order->order_type === 'delivery';
    $isPickup   = $order->order_type === 'pickup';
    $isDining   = $order->order_type === 'dining';

    $typeLabel = $isDining ? 'Dining' : ucfirst($order->order_type);

    // Read the stored fee rather than deriving it from
    // (total - subtotal) — an adjusted order rewrites both of those,
    // so the derived figure drifted. The column is the source of truth.
    $deliveryFee = $isDelivery ? (float) $order->delivery_fee : 0.0;

    $payment = $order->latestPayment;

    $paymentMethodLabels = [
        'cod'  => 'CASH ON DELIVERY',
        'card' => 'CARD',
    ];

    $isUnpaidCash = $payment
        && $payment->method === 'cod'
        && $payment->status === 'pending';
@endphp

    <div class="invoice-box">

        <div class="center bold invoice-title">{{ config('app.name', 'Restaurant') }}</div>

        <div class="meta-row">
            <span>Order #{{ $order->order_number }}</span>
            <span>{{ $typeLabel }}</span>
        </div>

        <div class="meta-row">
            <span>Date {{ $order->created_at->format('Y/m/d') }}</span>
            <span>{{ $order->created_at->format('H:i') }}</span>
        </div>

        @if ($order->is_pre_order && $order->pre_order_date)
            <div class="schedule-box">
                {{ $isDining ? 'TABLE BOOKED' : 'SCHEDULED' }}<br>
                {{ \Illuminate\Support\Carbon::parse($order->pre_order_date)->format('D d/m/Y') }}@if ($order->pre_order_time) &middot; {{ \Illuminate\Support\Carbon::parse($order->pre_order_time)->format('H:i') }}@endif
            </div>
        @endif

        <div class="section-title bold">Order Invoice</div>

        <div class="address-block">
            <div class="label">{{ strtoupper($typeLabel) }}</div>
            <div>{{ $order->first_name }} {{ $order->last_name }}</div>
            <div>{{ $order->phone }}</div>

            @if ($isDelivery)
                <div>{{ $order->address }}@if ($order->apartment), {{ $order->apartment }}@endif</div>
                <div>{{ $order->city }}@if ($order->postcode) {{ $order->postcode }}@endif</div>
            @endif

            @if ($order->notes)
                <div>Note: {{ $order->notes }}</div>
            @endif
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="num">Price</th>
                    <th class="num">Qty</th>
                    <th class="num">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td class="num">£{{ number_format($item->unit_price, 2) }}</td>
                        <td class="num">{{ $item->quantity }}</td>
                        <td class="num">£{{ number_format($item->line_total, 2) }}</td>
                    </tr>

                    @if ($item->deal_name || ! empty($item->options))
                        <tr>
                            <td colspan="4" style="padding-top:0;">
                                <div class="item-options">
                                    @if ($item->deal_name)
                                        <div class="opt-line"><span>Deal: {{ $item->deal_name }}</span></div>
                                    @endif
                                    @foreach ($item->options ?? [] as $option)
                                        <div class="opt-line">
                                            <span>+ {{ $option['name'] }}</span>
                                            @if (($option['price_delta'] ?? 0) > 0)
                                                <span>£{{ number_format($option['price_delta'], 2) }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <div class="totals-row">
            <span>Sub-Total</span>
            <span>£{{ number_format($order->subtotal, 2) }}</span>
        </div>

        @if ($isDelivery)
            <div class="totals-row">
                <span>Delivery Fee</span>
                <span>{{ $deliveryFee > 0 ? '£' . number_format($deliveryFee, 2) : 'FREE' }}</span>
            </div>
        @endif

        <div class="totals-row grand">
            <span>Grand Total</span>
            <span>£{{ number_format($order->total, 2) }}</span>
        </div>

        {{-- PAYMENT --}}
        @if ($payment)
            <div class="payment-block">
                <div class="payment-title">PAYMENT</div>

                <div class="payment-row">
                    <span>Method</span>
                    <span>{{ $paymentMethodLabels[$payment->method] ?? strtoupper($payment->method) }}</span>
                </div>

                <div class="payment-row">
                    <span>Status</span>
                    <span>{{ strtoupper($payment->status) }}</span>
                </div>

                @if ($payment->transaction_id)
                    <div class="payment-row">
                        <span>Txn</span>
                        <span>{{ $payment->transaction_id }}</span>
                    </div>
                @endif

                @if ($payment->paid_at)
                    <div class="payment-row">
                        <span>Paid</span>
                        <span>{{ $payment->paid_at->format('d/m/Y H:i') }}</span>
                    </div>
                @endif

                @if ($isUnpaidCash)
                    <div class="payment-due">
                        COLLECT £{{ number_format($order->total, 2) }}
                    </div>
                @endif
            </div>
        @endif

        <div class="thank-you">
            {{ $isDining ? 'Enjoy your meal.' : 'Thank you for your order.' }}
        </div>

        <div class="footer-info">
            TEL: {{ config('restaurant.phone', '') }}<br>
            @if (config('restaurant.vat_number'))
                VAT NO: {{ config('restaurant.vat_number') }}
            @endif
        </div>

        <div class="qr-wrap">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('storefront.order.confirmation', $order->order_number)) }}" alt="QR Code">
        </div>

    </div>

</body>
</html>