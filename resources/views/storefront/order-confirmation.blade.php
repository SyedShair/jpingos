@extends('storefront.layouts.app')

@section('title', 'Order Confirmed | ' . config('app.name', 'Restaurant'))

@section('content')

@php
    $isDelivery = $order->order_type === 'delivery';
    $isPickup   = $order->order_type === 'pickup';
    $isDining   = $order->order_type === 'dining';

    $detailsHeading = $isDining
        ? 'Reservation details'
        : ($isPickup ? 'Pickup details' : 'Delivery details');

    $lead = $isDining
        ? 'Your table is booked. We look forward to seeing you.'
        : ($isPickup
            ? "We'll text you when your order is ready to collect."
            : "Your order is being prepared and will be on its way soon.");

    $paymentMethodLabels = [
        'cod'  => 'Cash on Delivery',
        'card' => 'Card',
    ];

    $paymentStatusLabels = [
        'pending'  => 'Pending',
        'paid'     => 'Paid',
        'failed'   => 'Failed',
        'refunded' => 'Refunded',
        'cancelled' => 'Cancelled',
    ];

    $paymentStatusColors = [
        'pending'   => '#8B7F73',
        'paid'      => '#1a9c5c',
        'failed'    => '#b22b40',
        'refunded'  => '#6b7280',
        'cancelled' => '#b22b40',
    ];

    $payment = $order->latestPayment;
@endphp

<div class="section section-margin">
    <div class="container">

        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-6">
                <i class="fa fa-check-circle" style="font-size: 48px; color: #1a9c5c;"></i>
                <h1 class="mt-3">Thank you, {{ $order->first_name }}!</h1>
                <p class="text-muted mb-1">
                    Your order <strong>{{ $order->order_number }}</strong> has been placed.
                </p>
                <p class="text-muted mb-0">{{ $lead }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">

                @if ($order->is_pre_order && $order->pre_order_date)
                    <div class="mb-5 p-3"
                         style="background:#fdf6e9; border:1px solid #f0dca3; border-radius:8px; display:flex; align-items:flex-start; gap:12px;">
                        <i class="fa fa-calendar" style="color:#c99a1f; margin-top:3px;"></i>
                        <div>
                            <p class="mb-1" style="font-weight:700; color:#7a5c12;">
                                {{ $isDining ? 'Table booked for' : 'Scheduled for' }}
                            </p>
                            <p class="mb-0" style="color:#7a5c12;">
                                {{ \Illuminate\Support\Carbon::parse($order->pre_order_date)->format('l, j F Y') }}
                                @if ($order->pre_order_time)
                                    at {{ \Illuminate\Support\Carbon::parse($order->pre_order_time)->format('g:i A') }}
                                @endif
                            </p>
                        </div>
                    </div>
                @endif

                <div class="table-responsive mb-6">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>
                                        {{ $item->name }}
                                        @if ($item->options)
                                            <br>
                                            <span class="small text-muted">
                                                {{ collect($item->options)->pluck('name')->join(', ') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>£{{ number_format($item->line_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row">

                    <div class="col-md-6 mb-4 mb-md-0">
                        <h5>{{ $detailsHeading }}</h5>

                        <p class="mb-1">{{ $order->name }}</p>
                        <p class="mb-1">{{ $order->phone }}</p>
                        <p class="mb-1">{{ $order->email }}</p>

                        @if ($isDelivery)
                            <p class="mb-1 mt-3">
                                {{ $order->address }}@if ($order->apartment), {{ $order->apartment }}@endif
                            </p>
                            <p class="mb-1">
                                {{ $order->city }}@if ($order->postcode) {{ $order->postcode }}@endif
                            </p>

                            @if ($order->notes)
                                <p class="mb-1 mt-2 small text-muted fst-italic">
                                    Note: {{ $order->notes }}
                                </p>
                            @endif

                        @elseif ($isPickup)
                            <p class="mb-1 mt-3">
                                Collect from
                                <strong>{{ config('restaurant.pickup_address', 'our Nottingham location') }}</strong>
                            </p>

                        @else
                            <p class="mb-1 mt-3">
                                Dining in at
                                <strong>{{ config('restaurant.pickup_address', 'our Nottingham location') }}</strong>
                            </p>
                        @endif

                        @if ($payment)
                            <h5 class="mt-4">Payment</h5>
                            <p class="mb-0">
                                {{ $paymentMethodLabels[$payment->method] ?? ucfirst($payment->method) }}
                                <span
                                    class="ms-1"
                                    style="display:inline-block; padding:2px 10px; border-radius:20px; font-size:12px; font-weight:700; color:#fff; background:{{ $paymentStatusColors[$payment->status] ?? '#8B7F73' }};"
                                >
                                    {{ $paymentStatusLabels[$payment->status] ?? ucfirst($payment->status) }}
                                </span>
                            </p>
                        @endif
                    </div>

                    <div class="col-md-6 text-md-end">
                        <h5>Subtotal</h5>
                        <p class="mb-3" style="font-size: 18px;">
                            £{{ number_format($order->subtotal, 2) }}
                        </p>

                        @if ($isDelivery)
                            <h5>Delivery fee</h5>
                            <p class="mb-3" style="font-size: 18px;">
                                {{ $order->delivery_fee > 0
                                    ? '£' . number_format($order->delivery_fee, 2)
                                    : 'Free' }}
                            </p>
                        @endif

                        <h5>Total</h5>
                        <p class="mb-0" style="font-size: 20px; font-weight: 700;">
                            £{{ number_format($order->total, 2) }}
                        </p>
                    </div>

                </div>

                <div class="text-center mt-6">
                    <a href="{{ route('storefront.home') }}" class="btn btn-dark btn-hover-primary rounded-0">
                        Continue Shopping
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection