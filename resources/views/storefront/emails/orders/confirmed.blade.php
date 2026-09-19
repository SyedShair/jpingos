<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed — {{ $order->order_number }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f7f7f8; font-family: Arial, Helvetica, sans-serif; color:#1A1A1A;">

@php
    $isDelivery = $order->order_type === 'delivery';
    $isPickup   = $order->order_type === 'pickup';
    $isDining   = $order->order_type === 'dining';

    $detailsHeading = $isDining
        ? 'Reservation Details'
        : ($isPickup ? 'Pickup Details' : 'Delivery Details');

    $lead = $isDining
        ? 'Your table is booked. We look forward to seeing you.'
        : ($isPickup
            ? "We'll text you when your order is ready to collect."
            : 'Your order has been received and is being prepared.');

    $payment = $order->latestPayment;

    $paymentMethodLabels = [
        'cod'  => 'Cash on Delivery',
        'card' => 'Card',
    ];

    $paymentStatusLabels = [
        'pending'   => 'Awaiting payment',
        'paid'      => 'Paid',
        'failed'    => 'Payment failed',
        'refunded'  => 'Refunded',
        'cancelled' => 'Cancelled',
    ];

    // Cash orders are the only ones where the customer needs to *do*
    // something on the day, so they get an explicit prompt rather than
    // just a bare status label.
    $isUnpaidCash = $payment
        && $payment->method === 'cod'
        && $payment->status === 'pending';
@endphp

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f7f7f8; padding: 32px 0;">
        <tr>
            <td align="center">

                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:10px; overflow:hidden; max-width:600px; width:100%;">

                    {{-- HEADER --}}
                    <tr>
                        <td style="background-color:#1A1A1A; padding:28px 32px;">
                            <span style="color:#ffffff; font-size:20px; font-weight:bold;">
                                {{ config('app.name', 'Restaurant') }}
                            </span>
                        </td>
                    </tr>

                    {{-- CONFIRMATION MESSAGE --}}
                    <tr>
                        <td style="padding:32px 32px 8px;">
                            <p style="margin:0 0 6px; font-size:22px; font-weight:bold; color:#1A1A1A;">
                                Thanks, {{ $order->first_name }}!
                            </p>
                            <p style="margin:0; font-size:15px; color:#6b7280;">
                                {{ $lead }}
                            </p>
                        </td>
                    </tr>

                    {{-- ORDER NUMBER --}}
                    <tr>
                        <td style="padding:16px 32px 24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAF5EE; border:1px dashed #D9A441; border-radius:8px;">
                                <tr>
                                    <td style="padding:14px 18px; font-size:14px; color:#1A1A1A;">
                                        Order number
                                        <strong style="display:block; font-size:17px; margin-top:2px;">
                                            {{ $order->order_number }}
                                        </strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- SCHEDULED TIME (pre-orders only) --}}
                    @if ($order->is_pre_order && $order->pre_order_date)
                        <tr>
                            <td style="padding:0 32px 24px;">
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fdf6e9; border:1px solid #f0dca3; border-radius:8px;">
                                    <tr>
                                        <td style="padding:14px 18px; font-size:14px; color:#7a5c12;">
                                            {{ $isDining ? 'Table booked for' : 'Scheduled for' }}
                                            <strong style="display:block; font-size:16px; margin-top:2px;">
                                                {{ \Illuminate\Support\Carbon::parse($order->pre_order_date)->format('l, j F Y') }}@if ($order->pre_order_time) at {{ \Illuminate\Support\Carbon::parse($order->pre_order_time)->format('g:i A') }}@endif
                                            </strong>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    {{-- ORDERED ITEMS --}}
                    <tr>
                        <td style="padding:0 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #EAE0D3;">

                                @foreach ($order->items as $item)
                                    <tr>
                                        <td style="padding:16px 0; border-bottom:1px solid #EAE0D3;" valign="top">

                                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td valign="top">
                                                        <p style="margin:0; font-size:14px; font-weight:bold; color:#1A1A1A;">
                                                            {{ $item->quantity }} &times; {{ $item->name }}
                                                        </p>

                                                        @if ($item->options)
                                                            <p style="margin:4px 0 0; font-size:12px; color:#8B7F73;">
                                                                {{ collect($item->options)->pluck('name')->join(', ') }}
                                                            </p>
                                                        @endif
                                                    </td>
                                                    <td valign="top" align="right" style="white-space:nowrap;">
                                                        <p style="margin:0; font-size:14px; font-weight:bold; color:#1A1A1A;">
                                                            £{{ number_format($item->line_total, 2) }}
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>

                                        </td>
                                    </tr>
                                @endforeach

                            </table>
                        </td>
                    </tr>

                    {{-- TOTALS --}}
                    <tr>
                        <td style="padding:20px 32px 8px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="font-size:14px; color:#6b7280; padding:4px 0;">Subtotal</td>
                                    <td align="right" style="font-size:14px; color:#1A1A1A; padding:4px 0;">
                                        £{{ number_format($order->subtotal, 2) }}
                                    </td>
                                </tr>

                                @if ($isDelivery)
                                    <tr>
                                        <td style="font-size:14px; color:#6b7280; padding:4px 0;">Delivery fee</td>
                                        <td align="right" style="font-size:14px; color:#1A1A1A; padding:4px 0;">
                                            {{ $order->delivery_fee > 0
                                                ? '£' . number_format($order->delivery_fee, 2)
                                                : 'Free' }}
                                        </td>
                                    </tr>
                                @endif

                                <tr>
                                    <td style="font-size:16px; font-weight:bold; color:#1A1A1A; padding:10px 0 0; border-top:1px solid #EAE0D3;">
                                        Total
                                    </td>
                                    <td align="right" style="font-size:18px; font-weight:bold; color:#1A1A1A; padding:10px 0 0; border-top:1px solid #EAE0D3;">
                                        £{{ number_format($order->total, 2) }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- PAYMENT --}}
                    @if ($payment)
                        <tr>
                            <td style="padding:20px 32px 0;">
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:{{ $isUnpaidCash ? '#fdf6e9' : '#f7f7f8' }}; border:1px solid {{ $isUnpaidCash ? '#f0dca3' : '#EAE0D3' }}; border-radius:8px;">
                                    <tr>
                                        <td style="padding:18px 20px;">
                                            <p style="margin:0 0 10px; font-size:13px; font-weight:bold; letter-spacing:.04em; color:#8B7F73;">
                                                Payment
                                            </p>

                                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="font-size:14px; color:#6b7280; padding:3px 0;">Method</td>
                                                    <td align="right" style="font-size:14px; color:#1A1A1A; font-weight:bold; padding:3px 0;">
                                                        {{ $paymentMethodLabels[$payment->method] ?? ucfirst($payment->method) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size:14px; color:#6b7280; padding:3px 0;">Status</td>
                                                    <td align="right" style="font-size:14px; color:#1A1A1A; font-weight:bold; padding:3px 0;">
                                                        {{ $paymentStatusLabels[$payment->status] ?? ucfirst($payment->status) }}
                                                    </td>
                                                </tr>
                                                @if ($payment->paid_at)
                                                    <tr>
                                                        <td style="font-size:14px; color:#6b7280; padding:3px 0;">Paid on</td>
                                                        <td align="right" style="font-size:14px; color:#1A1A1A; padding:3px 0;">
                                                            {{ $payment->paid_at->format('j M Y, g:i A') }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            </table>

                                            @if ($isUnpaidCash)
                                                <p style="margin:12px 0 0; font-size:13px; color:#7a5c12;">
                                                    Please have
                                                    <strong>£{{ number_format($order->total, 2) }}</strong>
                                                    ready {{ $isDelivery ? 'for the driver' : 'when you arrive' }}.
                                                </p>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    {{-- DELIVERY / PICKUP / DINING DETAILS --}}

                    <tr>
                        <td style="padding:24px 32px 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f7f7f8; border-radius:8px;">
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <p style="margin:0 0 10px; font-size:13px; font-weight:bold; letter-spacing:.04em; color:#8B7F73;">
                                            {{ $detailsHeading }}
                                        </p>

                                        <p style="margin:0 0 4px; font-size:14px; color:#1A1A1A;">{{ $order->name }}</p>

                                        @if ($isDelivery)
                                            <p style="margin:0 0 4px; font-size:14px; color:#1A1A1A;">
                                                {{ $order->address }}@if ($order->apartment), {{ $order->apartment }}@endif
                                            </p>
                                            <p style="margin:0 0 4px; font-size:14px; color:#1A1A1A;">
                                                {{ $order->city }}@if ($order->postcode) {{ $order->postcode }}@endif
                                            </p>
                                        @else
                                            <p style="margin:0 0 4px; font-size:14px; color:#1A1A1A;">
                                                {{ $isDining ? 'Dining in at' : 'Collect from' }}
                                                {{ config('restaurant.pickup_address', 'our Nottingham location') }}
                                            </p>
                                        @endif

                                        <p style="margin:0; font-size:14px; color:#1A1A1A;">{{ $order->phone }}</p>

                                        @if ($order->notes)
                                            <p style="margin:10px 0 0; font-size:13px; color:#6b7280; font-style:italic;">
                                                Note: {{ $order->notes }}
                                            </p>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td style="padding:20px 32px 32px; border-top:1px solid #EAE0D3;" align="center">
                            <p style="margin:0; font-size:12px; color:#8B7F73;">
                                {{ config('app.name', 'Restaurant') }} — questions about your order? Just reply to this email.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>