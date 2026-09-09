@extends('storefront.layouts.app')

@section('title', 'Shopping Cart | ' . config('app.name', 'Restaurant'))

@section('content')

    {{-- =========================================================
         BREADCRUMB
    ========================================================= --}}

    <div class="section">

        <div class="breadcrumb-area bg-light">
            <div class="container-fluid">
                <div class="breadcrumb-content text-center">
                    <h1 class="title">Shopping Cart</h1>
                    <ul>
                        <li>
                            <a href="{{ route('storefront.home') }}">Home</a>
                        </li>
                        <li class="active">Shopping Cart</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    {{-- =========================================================
         SHOPPING CART
    ========================================================= --}}

    <div class="section section-margin" id="cart-page">
        <div class="container">

            @if ($cartItems->isEmpty())

                <div class="row">
                    <div class="col-12 text-center">
                        <p class="mb-4">Your cart is empty.</p>
                        <a href="{{ route('storefront.home') }}" class="btn btn-dark btn-hover-primary rounded-0">
                            Browse Menu
                        </a>
                    </div>
                </div>

            @else

                <div class="row">
                    <div class="col-12">

                        <div class="cart-table table-responsive">
                            <table class="table table-bordered">

                                <thead>
                                    <tr>
                                        <th class="pro-thumbnail">Image</th>
                                        <th class="pro-title">Product</th>
                                        <th class="pro-price">Price</th>
                                        <th class="pro-quantity">Quantity</th>
                                        <th class="pro-subtotal">Total</th>
                                        <th class="pro-remove">Remove</th>
                                    </tr>
                                </thead>

                                <tbody id="cart-page-rows">

                                    @foreach ($cartItems as $cartItem)

                                        <tr data-row-id="{{ $cartItem->row_id }}">

                                            <td class="pro-thumbnail">
                                                <a href="{{ $cartItem->is_bundle ? '#' : route('storefront.dish', $cartItem->menuItem->slug) }}">
                                                    <img class="img-fluid" style="width: 100px;" src="{{ $cartItem->menuItem->image_url }}" alt="{{ $cartItem->menuItem->name }}" />
                                                </a>
                                            </td>

                                            <td class="pro-title">
                                                <a href="{{ $cartItem->is_bundle ? '#' : route('storefront.dish', $cartItem->menuItem->slug) }}">
                                                    {{ $cartItem->menuItem->name }}
                                                </a>

                                                @if ($cartItem->deal_name)
                                                    <br>
                                                    <span class="small text-muted">Deal: {{ $cartItem->deal_name }}</span>
                                                @endif

                                                @if ($cartItem->options->isNotEmpty())
                                                    <br>
                                                    <span class="small text-muted">
                                                        {{ $cartItem->options->pluck('name')->join(', ') }}
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="pro-price">
                                                <span>£{{ number_format($cartItem->unit_price, 2) }}</span>
                                            </td>

                                            <td class="pro-quantity">
                                                <div class="quantity">
                                                    <div class="cart-plus-minus">
                                                        <input
                                                            class="cart-plus-minus-box js-cart-qty-input"
                                                            data-row-id="{{ $cartItem->row_id }}"
                                                            value="{{ $cartItem->quantity }}"
                                                            type="text"
                                                        >
                                                        <div class="dec qtybutton">-</div>
                                                        <div class="inc qtybutton">+</div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="pro-subtotal">
                                                <span class="js-cart-row-total">£{{ number_format($cartItem->line_total, 2) }}</span>
                                            </td>

                                            <td class="pro-remove">
                                                <a href="javascript:void(0)" class="js-cart-remove" data-row-id="{{ $cartItem->row_id }}">
                                                    <i class="pe-7s-trash"></i>
                                                </a>
                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>
                        </div>

                        {{-- Coupon UI only — no promo_code redemption logic exists yet
                             to wire this to. Left as a visual placeholder from the
                             template rather than a working form, since a fake
                             "Apply" that does nothing would be worse than being
                             upfront it isn't built. --}}
                        <div class="cart-update-option d-block d-md-flex justify-content-between">

                            <div class="apply-coupon-wrapper">
                                <form action="#" method="post" class="d-block d-md-flex" onsubmit="return false;">
                                    <input type="text" placeholder="Enter Your Coupon Code" disabled />
                                    <button class="btn btn-dark btn-hover-primary rounded-0" disabled type="button">
                                        Apply Coupon
                                    </button>
                                </form>
                            </div>

                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-5 ms-auto col-custom">

                        <div class="cart-calculator-wrapper">

                            <div class="cart-calculate-items">

                                <h3 class="title">Cart Totals</h3>

                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <td>Sub Total</td>
                                            <td id="cart-page-subtotal">£{{ number_format($cartSubtotal, 2) }}</td>
                                        </tr>
                                        <tr class="total">
                                            <td>Total</td>
                                            <td class="total-amount" id="cart-page-total">£{{ number_format($cartSubtotal, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>

                            </div>

                            <a href="{{ route('storefront.checkout') }}" class="btn btn-dark btn-hover-primary rounded-0 w-100">
                                Proceed To Checkout
                            </a>

                        </div>

                    </div>
                </div>

            @endif

        </div>
    </div>

@endsection

@push('scripts')
<script>
(function ($) {
    "use strict";

    function refreshRow($input, data) {
        // Backend response doesn't return per-row totals — only cart-wide
        // count/subtotal/html. Recomputing this row's total client-side
        // from unit price × new quantity, read off the row itself, is
        // simpler than fetching detailedItems() again just for one row.
        const $row = $input.closest('tr');
        const unitPrice = parseFloat(
            $row.find('.pro-price span').text().replace('£', '')
        );
        const qty = parseInt($input.val(), 10) || 1;

        $row.find('.js-cart-row-total').text('£' + (unitPrice * qty).toFixed(2));
        $('#cart-page-subtotal').text('£' + data.subtotal);
        $('#cart-page-total').text('£' + data.subtotal);
        $('.header-action-btn-cart .header-action-num').text(data.count);
    }

    function updateQuantity($input) {
        const rowId = $input.data('row-id');
        const qty = parseInt($input.val(), 10) || 1;

        fetch('/cart/' + rowId, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({ quantity: qty })
        })
        .then(res => res.json())
        .then(data => refreshRow($input, data));
    }

    $(document).on('click', '#cart-page-rows .qtybutton', function () {
        const $box = $(this).closest('.cart-plus-minus').find('.js-cart-qty-input');
        let qty = parseInt($box.val(), 10) || 1;

        qty = $(this).hasClass('inc') ? qty + 1 : Math.max(1, qty - 1);

        $box.val(qty);
        updateQuantity($box);
    });

    $(document).on('change', '#cart-page-rows .js-cart-qty-input', function () {
        updateQuantity($(this));
    });

    $(document).on('click', '#cart-page-rows .js-cart-remove', function () {
        const rowId = $(this).data('row-id');

        fetch('/cart/' + rowId, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(res => res.json())
        .then(function () {
            // Simplest correct behaviour: reload so totals, empty-cart
            // state, and the header badge all end up consistent without
            // reimplementing the empty-cart markup in JS.
            window.location.reload();
        });
    });

})(jQuery);
</script>
@endpush