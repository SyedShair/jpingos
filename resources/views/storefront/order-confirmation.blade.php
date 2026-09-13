@extends('storefront.layouts.app')

@section('title', 'Order Confirmed | ' . config('app.name', 'Restaurant'))

@section('content')

<div class="section section-margin">
    <div class="container">

        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-6">
                <i class="fa fa-check-circle" style="font-size: 48px; color: #1a9c5c;"></i>
                <h1 class="mt-3">Thank you, {{ $order->first_name }}!</h1>
                <p class="text-muted">
                    Your order <strong>{{ $order->order_number }}</strong> has been placed.
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">

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
                    <div class="col-md-6">
                        <h5>Delivery details</h5>
                        <p class="mb-1">{{ $order->name }}</p>
                        <p class="mb-1">{{ $order->address }}@if ($order->apartment), {{ $order->apartment }}@endif</p>
                        <p class="mb-1">{{ $order->city }} @if ($order->postcode) {{ $order->postcode }} @endif</p>
                        <p class="mb-1">{{ $order->phone }}</p>
                        <p class="mb-1 text-capitalize">{{ $order->order_type }}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
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