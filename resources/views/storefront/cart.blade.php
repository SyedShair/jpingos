@extends('storefront.layouts.app')

@section('title', 'Shopping Cart | ' . config('app.name', 'Restaurant'))

@section('content')

    <!-- {{-- =========================================================
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

    </div> -->

    {{-- =========================================================
         SHOPPING CART
    ========================================================= --}}

    <div class="section section-margin" id="cart-page">
        <div class="container">

            {{-- Empty state — hidden by default when the cart has items;
                 shown by JS (no reload) once the last row is removed,
                 or rendered directly on page load if the cart was
                 already empty when the page loaded. --}}
            <div class="row" id="cart-page-empty" style="{{ $cartItems->isEmpty() ? '' : 'display:none;' }}">
                <div class="col-12 text-center">
                    <p class="mb-4">Your cart is empty.</p>
                    <a href="{{ route('storefront.home') }}" class="btn btn-dark btn-hover-primary rounded-0">
                        Browse Menu
                    </a>
                </div>
            </div>

            <div id="cart-page-content" style="{{ $cartItems->isEmpty() ? 'display:none;' : '' }}">

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
                                                    @php
                                                        $complimentary = $cartItem->options->filter(fn ($opt) => (float) $opt->price_delta <= 0);
                                                        $extras = $cartItem->options->filter(fn ($opt) => (float) $opt->price_delta > 0);
                                                    @endphp

                                                    @if ($complimentary->isNotEmpty())
                                                        <br>
                                                        <span class="small text-muted">
                                                            <strong>Complimentary:</strong> {{ $complimentary->pluck('name')->join(', ') }}
                                                        </span>
                                                    @endif

                                                    @if ($extras->isNotEmpty())
                                                        <br>
                                                        <span class="small text-muted">
                                                            <strong>Extras:</strong>
                                                            {{ $extras->map(fn ($opt) => $opt->name.' (+£'.number_format($opt->price_delta, 2).')')->join(', ') }}
                                                        </span>
                                                    @endif
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
                        <!-- <div class="cart-update-option d-block d-md-flex justify-content-between">

                            <div class="apply-coupon-wrapper">
                                <form action="#" method="post" class="d-block d-md-flex" onsubmit="return false;">
                                    <input type="text" placeholder="Enter Your Coupon Code" disabled />
                                    <button class="btn btn-dark btn-hover-primary rounded-0" disabled type="button">
                                        Apply Coupon
                                    </button>
                                </form>
                            </div>

                        </div> -->

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

            </div>

        </div>
    </div>

@endsection

@push('scripts')
<script>
(function ($) {
    "use strict";

    function refreshRow($input, data) {
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

    /* ==========================================
       REMOVE CART ITEM
    ========================================== */
    $(document).on('click', '.js-cart-remove', function () {

        const rowId = $(this).data('row-id');
        const $row = $(this).closest('tr');

        fetch('/cart/' + rowId, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('Could not remove item.');
            }
            return response.json();
        })
        .then(function (data) {

            $row.remove();

            if (data.count === 0) {
                $('#cart-page-content').hide();
                $('#cart-page-empty').show();
                $('.header-action-btn-cart .header-action-num').text(0);
                return;
            }

            $('#cart-page-subtotal').text('£' + data.subtotal);
            $('#cart-page-total').text('£' + data.subtotal);
            $('.header-action-btn-cart .header-action-num').text(data.count);
        })
        .catch(function (error) {
            console.error(error);
            alert(error.message);
        });

    });

})(jQuery);
</script>
@endpush