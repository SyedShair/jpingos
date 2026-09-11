<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout | {{ config('app.name', 'Restaurant') }}</title>
    <link rel="shortcut icon" href="{{ asset('storefront/assets/images/favicon.ico') }}"> 

    <link rel="stylesheet" href="{{ asset('storefront/assets/css/vendor.min.css') }}">  
    <link rel="stylesheet" href="{{ asset('storefront/assets/css/plugins.min.css') }}">  
    <link rel="stylesheet" href="{{ asset('storefront/assets/css/style.min.css') }}">

    <style>
        .checkout-page {  
            --green: #1a9c5c;  
            --charcoal: #1A1A1A;  
            --line: #e5e5e5;  
            --muted: #6b7280;  
            --error: #b22b40;  
            background: #fff;  
            padding: 60px 0;  
        }

        .checkout-page .checkout-breadcrumb {  
            text-align: center;  
            font-size: 14px;  
            color: var(--muted);  
            margin-bottom: 32px;  
        }

        .checkout-page .checkout-breadcrumb .active {  
            color: var(--charcoal);  
            font-weight: 700;  
        }

        .checkout-page .checkout-breadcrumb i {  
            margin: 0 8px;  
            font-size: 11px;  
        }

        .checkout-page .checkout-login-note {  
            font-size: 14px;  
            color: var(--muted);  
        }

        .checkout-page .checkout-login-note a {  
            color: var(--green);  
            font-weight: 700;  
        }

        .checkout-page .checkout-section-title {  
            font-size: 20px;  
            font-weight: 700;  
            color: var(--charcoal);  
            margin-bottom: 16px;  
        }

        .checkout-page .form-control,  
        .checkout-page .form-select {  
            border: 1px solid var(--line);  
            border-radius: 6px;  
            padding: 14px 16px;  
            font-size: 15px;  
            margin-bottom: 6px;  
            transition: border-color .2s ease, box-shadow .2s ease;  
        }

        .checkout-page .form-control:focus,  
        .checkout-page .form-select:focus {  
            border-color: var(--green);  
            box-shadow: 0 0 0 0.2rem rgba(26, 156, 92, .15);  
        }

        @keyframes field-shake {  
            10%, 90% { transform: translateX(-1px); }  
            20%, 80% { transform: translateX(2px); }  
            30%, 50%, 70% { transform: translateX(-4px); }  
            40%, 60% { transform: translateX(4px); }  
        }

        .checkout-page .form-control.border-color,  
        .checkout-page .form-select.border-color {  
            border-color: var(--error);  
            animation: field-shake .4s ease;  
        }

        .checkout-page .field-error {  
            display: block;  
            max-height: 0;  
            overflow: hidden;  
            opacity: 0;  
            color: var(--error);  
            font-size: 13px;  
            margin-bottom: 0;  
            transition: max-height .25s ease, opacity .2s ease, margin-bottom .25s ease;  
        }

        .checkout-page .field-error.show {  
            max-height: 24px;  
            opacity: 1;  
            margin-bottom: 10px;  
        }

        .checkout-page .form-row {  
            display: flex;  
            gap: 14px;  
        }

        .checkout-page .form-row > div {  
            flex: 1 1 50%;  
        }

        .checkout-page .field-hint {  
            font-size: 13px;  
            font-style: italic;  
            color: var(--muted);  
            margin-bottom: 14px;  
        }

        .checkout-page .delivery-check-inline {  
            font-size: 13px;  
            font-weight: 600;  
            max-height: 0;  
            overflow: hidden;  
            opacity: 0;  
            margin-bottom: 0;  
            transition: max-height .25s ease, opacity .2s ease, margin-bottom .25s ease;  
        }

        .checkout-page .delivery-check-inline.show {  
            max-height: 24px;  
            opacity: 1;  
            margin-bottom: 10px;  
        }

        .checkout-page .delivery-check-inline.is-available { color: var(--green); }  
        .checkout-page .delivery-check-inline.is-unavailable { color: var(--error); }  
        .checkout-page .delivery-check-inline.is-checking { color: var(--muted); }

        .checkout-page .form-check {  
            margin-bottom: 18px;  
        }

        .checkout-page .form-check-input:checked {  
            background-color: var(--green);  
            border-color: var(--green);  
        }

        .checkout-page .form-check-label {  
            color: var(--muted);  
            font-size: 14px;  
        }

        .checkout-page .checkout-actions {  
            display: flex;  
            align-items: center;  
            justify-content: space-between;  
            margin-top: 24px;  
            flex-wrap: wrap;  
            gap: 16px;  
        }

        .checkout-page .checkout-return-link {  
            color: var(--charcoal);  
            font-size: 14px;  
            font-weight: 600;  
        }

        .checkout-page .checkout-return-link i {  
            margin-right: 6px;  
        }

        .checkout-page .checkout-continue-btn {  
            background: var(--charcoal);  
            color: #fff;  
            border: none;  
            border-radius: 8px;  
            padding: 15px 32px;  
            font-weight: 700;  
            text-transform: uppercase;  
            letter-spacing: .02em;  
            font-size: 14px;  
            transition: background .2s ease, opacity .2s ease;  
        }

        .checkout-page .checkout-continue-btn:hover:not(:disabled) {  
            background: var(--green);  
        }

        .checkout-page .checkout-continue-btn:disabled {  
            opacity: .5;  
            cursor: not-allowed;  
            background: var(--muted);  
        }

        .checkout-page .checkout-footer-policies {  
            list-style: none;  
            display: flex;  
            flex-wrap: wrap;  
            gap: 20px;  
            padding: 0;  
            margin: 32px 0 0;  
            border-top: 1px solid var(--line);  
            padding-top: 20px;  
        }

        .checkout-page .checkout-footer-policies a {  
            font-size: 13px;  
            color: var(--muted);  
        }

        .checkout-page .checkout-footer-policies a:hover {  
            color: var(--charcoal);  
        }

        /* =========================================================  
           FULFILMENT METHOD (Delivery / Pickup)  
        ========================================================== */

        .checkout-page .fulfilment-toggle {  
            display: flex;  
            gap: 12px;  
            margin-bottom: 20px;  
        }

        .checkout-page .fulfilment-option {  
            flex: 1;  
            position: relative;  
        }

        .checkout-page .fulfilment-option input {  
            position: absolute;  
            opacity: 0;  
            inset: 0;  
            margin: 0;  
            cursor: pointer;  
        }

        .checkout-page .fulfilment-option label {  
            display: flex;  
            align-items: center;  
            gap: 10px;  
            border: 1px solid var(--line);  
            border-radius: 8px;  
            padding: 14px 16px;  
            font-weight: 600;  
            font-size: 14px;  
            color: var(--charcoal);  
            cursor: pointer;  
            transition: border-color .2s ease, background .2s ease;  
        }

        .checkout-page .fulfilment-option label i {  
            font-size: 16px;  
            color: var(--muted);  
        }

        .checkout-page .fulfilment-option input:checked + label {  
            border-color: var(--green);  
            background: #f0f9f4;  
        }

        .checkout-page .fulfilment-option input:checked + label i {  
            color: var(--green);  
        }

        .checkout-page .pickup-note {  
            display: flex;  
            align-items: flex-start;  
            gap: 10px;  
            background: #f7f7f8;  
            border: 1px solid var(--line);  
            border-radius: 8px;  
            padding: 14px 16px;  
            font-size: 13.5px;  
            color: var(--muted);  
            margin-bottom: 20px;  
        }

        .checkout-page .pickup-note i {  
            color: var(--green);  
            margin-top: 2px;  
        }

        .checkout-page .pickup-note strong {  
            color: var(--charcoal);  
        }

        .checkout-page .delivery-area-note {  
            display: flex;  
            align-items: center;  
            gap: 10px;  
            background: #f0f9f4;  
            border: 1px solid #cdeadb;  
            border-radius: 8px;  
            padding: 12px 16px;  
            font-size: 13.5px;  
            color: #146c40;  
            margin-bottom: 20px;  
        }

        .checkout-page .delivery-area-note i {  
            font-size: 16px;  
        }

        /* =========================================================  
           ORDER SUMMARY  
        ========================================================== */

        .checkout-page .order-summary-toggle {  
            display: none;  
            align-items: center;  
            justify-content: space-between;  
            background: #f7f7f8;  
            border: 1px solid var(--line);  
            border-radius: 10px;  
            padding: 14px 18px;  
            margin-bottom: 20px;  
            color: var(--charcoal);  
            font-weight: 600;  
            font-size: 14px;  
            cursor: pointer;  
        }

        .checkout-page .order-summary-toggle .toggle-label-show {  
            display: inline;  
        }

        .checkout-page .order-summary-toggle .toggle-label-hide {  
            display: none;  
        }

        .checkout-page .order-summary-toggle[aria-expanded="true"] .toggle-label-show {  
            display: none;  
        }

        .checkout-page .order-summary-toggle[aria-expanded="true"] .toggle-label-hide {  
            display: inline;  
        }

        .checkout-page .order-summary-toggle i.fa-angle-down {  
            transition: transform .2s ease;  
            margin-left: 8px;  
        }

        .checkout-page .order-summary-toggle[aria-expanded="true"] i.fa-angle-down {  
            transform: rotate(180deg);  
        }

        .checkout-page .order-summary {  
            background: #f7f7f8;  
            border-radius: 12px;  
            padding: 28px;  
            height: 100%;  
        }

        .checkout-page .summary-item {  
            display: flex;  
            align-items: flex-start;  
            gap: 14px;  
            margin-bottom: 20px;  
        }

        .checkout-page .summary-item-thumb {  
            position: relative;  
            flex-shrink: 0;  
            width: 64px;  
            height: 64px;  
        }

        .checkout-page .summary-item-thumb-img {  
            width: 100%;  
            height: 100%;  
            border-radius: 8px;  
            overflow: hidden;  
            border: 1px solid var(--line);  
            background: #fff;  
        }

        .checkout-page .summary-item-thumb-img img {  
            width: 100%;  
            height: 100%;  
            object-fit: cover;  
        }

        @keyframes badge-pop {  
            from { transform: scale(0); opacity: 0; }  
            to { transform: scale(1); opacity: 1; }  
        }

        .checkout-page .summary-item-thumb .qty-badge {  
            position: absolute;  
            top: -8px;  
            right: -8px;  
            background: #6b7280;  
            color: #fff;  
            width: 20px;  
            height: 20px;  
            border-radius: 50%;  
            display: flex;  
            align-items: center;  
            justify-content: center;  
            font-size: 11px;  
            font-weight: 700;  
            z-index: 2;  
            box-shadow: 0 1px 3px rgba(0,0,0,.2);  
            animation: badge-pop .3s ease;  
        }

        .checkout-page .summary-item-name {  
            flex: 1;  
            font-size: 13px;  
            font-weight: 700;  
            text-transform: uppercase;  
            color: var(--charcoal);  
            line-height: 1.4;  
        }

        .checkout-page .summary-item-deal,  
        .checkout-page .summary-item-options {  
            display: block;  
            font-size: 11px;  
            font-weight: 500;  
            text-transform: none;  
            color: var(--muted);  
            margin-top: 2px;  
            line-height: 1.4;  
        }

        .checkout-page .summary-item-extra-price {  
            color: var(--error);  
            font-weight: 700;  
        }

        .checkout-page .summary-item-price {  
            font-size: 14px;  
            font-weight: 600;  
            color: var(--charcoal);  
            white-space: nowrap;  
        }

        .checkout-page .summary-totals {  
            border-top: 1px solid var(--line);  
            padding-top: 18px;  
            margin-top: 6px;  
        }

        .checkout-page .summary-totals-row {  
            display: flex;  
            justify-content: space-between;  
            font-size: 14px;  
            color: var(--charcoal);  
            margin-bottom: 12px;  
        }

        .checkout-page .summary-totals-row.muted-value span:last-child {  
            color: var(--muted);  
            font-size: 13px;  
        }

        .checkout-page .summary-total-final {  
            border-top: 1px solid var(--line);  
            padding-top: 14px;  
            margin-top: 4px;  
            display: flex;  
            justify-content: space-between;  
            align-items: baseline;  
            font-size: 15px;  
            font-weight: 700;  
            color: var(--charcoal);  
        }

        .checkout-page .summary-total-final .amount {  
            font-size: 20px;  
        }

        @media (max-width: 991px) {
            .checkout-page .order-summary-toggle {  
                display: flex;  
            }

            .checkout-page .order-summary-collapse-wrap {  
                display: none;  
                margin-bottom: 32px;  
            }

            .checkout-page .order-summary-collapse-wrap.show {  
                display: block;  
            }
        }

        @media (min-width: 992px) {
            .checkout-page .order-summary-collapse-wrap {  
                display: block !important;  
            }
        }
    </style>
</head> 
<body> 

<div class="section checkout-page">  
    <div class="container">

        <div class="text-center mb-6">  
            <a href="{{ route('storefront.home') }}">  
                <img src="{{ asset('storefront/assets/images/logo/logo.png') }}" alt="{{ config('app.name', 'Restaurant') }}" style="max-height: 60px;">  
            </a>  
        </div>

        <div class="checkout-breadcrumb">  
            <a href="{{ route('storefront.cart') }}">Cart</a>  
            <i class="fa fa-chevron-right"></i>  
            <span class="active">Information</span>  
            <i class="fa fa-chevron-right"></i>  
            <span>Payment</span>  
        </div>

        <div class="row">

            <div class="col-lg-7 col-12 order-2 order-lg-1">

                <div   
                    class="order-summary-toggle"   
                    id="order-summary-toggle"   
                    role="button"   
                    tabindex="0"   
                    aria-expanded="false"   
                    aria-controls="order-summary-collapse-wrap"   
                >  
                    <span>  
                        <i class="fa fa-shopping-cart"></i>  
                        <span class="toggle-label-show">Show order summary</span>  
                        <span class="toggle-label-hide">Hide order summary</span>  
                        <i class="fa fa-angle-down"></i>  
                    </span>  
                    <span class="mobile-summary-total">£{{ number_format($cartSubtotal, 2) }}</span>  
                </div>

                <form id="checkout-info-form">

                    <div class="d-flex justify-content-between align-items-center mb-4">
    <div class="checkout-section-title mb-0">Contact information</div>

    <div class="checkout-login-note">
    @auth('customer')
        Logged in as <strong>{{ $customer->name }}</strong> —
      <a href=" {{ route('storefront.logout') }}">Log out</a> 
    @else
        Already have an account? <a href="{{ route('storefront.login') }}">Log in</a>
    @endauth
</div>
</div>

<input type="email" id="checkout-email" class="form-control" placeholder="Email"
    value="{{ $customer->email ?? '' }}" @if($customer) readonly @endif>
<div class="field-error" id="checkout-email-error"></div>


                    <div class="checkout-section-title">How would you like your order?</div>

                    <div class="fulfilment-toggle">

                        <div class="fulfilment-option">  
                            <input type="radio" name="fulfilment-method" id="fulfilment-delivery" value="delivery" checked>  
                            <label for="fulfilment-delivery">  
                                <i class="fa fa-motorcycle"></i>  
                                Delivery  
                            </label>  
                        </div>

                        <div class="fulfilment-option">  
                            <input type="radio" name="fulfilment-method" id="fulfilment-pickup" value="pickup">  
                            <label for="fulfilment-pickup">  
                                <i class="fa fa-store"></i>  
                                Pickup  
                            </label>  
                        </div>

                    </div>

                    <div id="delivery-fields">

                        <div class="delivery-area-note">  
                            <i class="fa fa-map-marker-alt"></i>  
                            <span>We currently deliver within Nottingham only.</span>  
                        </div>

                        <div class="form-row">  
                            <div>  
                                <input type="text" id="checkout-first-name" class="form-control" placeholder="First Name"
            value="{{ $customer->first_name ?? '' }}">
        <div class="field-error" id="checkout-first-name-error"></div>
                            </div>  
                            <div>  
                                <input type="text" id="checkout-last-name" class="form-control" placeholder="Last Name"
            value="{{ $customer->last_name ?? '' }}">
        <div class="field-error" id="checkout-last-name-error"></div>
                            </div>  
                        </div>

                        <div class="form-row">  
                            <div>  
                                <select class="form-select" id="checkout-city">  
                                    <option value="nottingham">Nottingham</option>  
                                </select>  
                                <div class="field-error" id="checkout-city-error"></div>  
                            </div>  
                            <div>  
                                <input type="text" id="checkout-zipcode" class="form-control" placeholder="Postcode" value="{{ $customer->postcode ?? '' }}">  
                                <div class="field-error" id="checkout-zipcode-error"></div>  
                                <div class="delivery-check-inline" id="checkout-zipcode-result"></div>  
                            </div>  
                        </div>

                        <input type="text" id="checkout-address" class="form-control" placeholder="Delivery Address" value="{{ $customer->address ?? '' }}">  
                        <div class="field-error" id="checkout-address-error"></div>  
                        <div class="field-hint">*kindly add your complete address in above field</div>

                        <input type="text" id="checkout-apartment" class="form-control" placeholder="Apartment, suite, unit etc. (optional)" value="{{ $customer->apartment ?? '' }}">

                        <input type="text" id="checkout-delivery-notes" class="form-control" placeholder="Delivery notes (optional) — e.g. gate code, floor, landmark">

                    </div>

                    <div id="pickup-fields" style="display: none;">

                        <div class="pickup-note">  
                            <i class="fa fa-store"></i>  
                            <span>  
                                Collect from  
                                <strong>{{ config('restaurant.pickup_address', 'our Nottingham location') }}</strong>.  
                                We'll text you when your order's ready.  
                            </span>  
                        </div>

                        <div class="form-row">  
                            <div>  
                                <input type="text" id="checkout-first-name-pickup" class="form-control" placeholder="First Name">  
                                <div class="field-error" id="checkout-first-name-pickup-error"></div>  
                            </div>  
                            <div>  
                                <input type="text" id="checkout-last-name-pickup" class="form-control" placeholder="Last Name">  
                                <div class="field-error" id="checkout-last-name-pickup-error"></div>  
                            </div>  
                        </div>

                    </div>

                    <input type="text" id="checkout-phone" class="form-control" placeholder="Phone Number">  
                    <div class="field-error" id="checkout-phone-error"></div>  
                    <div class="field-hint">*Phone number must be like this 07123456789</div>

                    @guest('customer')
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="checkout-save-info">
        <label class="form-check-label" for="checkout-save-info">
            Save this information for next time
        </label>
    </div>
@endguest

                    <!-- <div class="form-check">  
                        <input class="form-check-input" type="checkbox" id="checkout-text-offers">  
                        <label class="form-check-label" for="checkout-text-offers">  
                            Text me with news and offers  
                        </label>  
                    </div> -->

                    <div class="checkout-actions">  
                        <a href="{{ route('storefront.cart') }}" class="checkout-return-link">  
                            <i class="fa fa-chevron-left"></i> Return to cart  
                        </a>  
                        <button type="button" id="checkout-continue-btn" class="checkout-continue-btn">  
                            Continue to Payment  
                        </button>  
                    </div>

                </form>

                <ul class="checkout-footer-policies">  
                    <li><a href="javascript:void(0)">Refund policy</a></li>  
                    <li><a href="javascript:void(0)">Privacy policy</a></li>  
                    <li><a href="javascript:void(0)">Terms of service</a></li>  
                </ul>

            </div>

            <div class="col-lg-5 col-12 order-1 order-lg-2 mb-6 mb-lg-0">

                <div class="order-summary-collapse-wrap" id="order-summary-collapse-wrap">

                    <div class="order-summary">

                        @foreach ($cartItems as $cartItem)

                            <div class="summary-item">

                                <div class="summary-item-thumb">  
                                    <div class="summary-item-thumb-img">  
                                        <img src="{{ $cartItem->menuItem->image_url }}" alt="{{ $cartItem->menuItem->name }}">  
                                    </div>  
                                    <span class="qty-badge">{{ $cartItem->quantity }}</span>  
                                </div>

                                <div class="summary-item-name">  
                                    {{ $cartItem->menuItem->name }}

                                    @if ($cartItem->deal_name)  
                                        <span class="summary-item-deal">Deal: {{ $cartItem->deal_name }}</span>  
                                    @endif

                                    @if ($cartItem->options->isNotEmpty())  
                                        @php  
                                            $complimentary = $cartItem->options->filter(fn ($opt) => (float) $opt->price_delta <= 0);  
                                            $extras = $cartItem->options->filter(fn ($opt) => (float) $opt->price_delta > 0);  
                                        @endphp

                                        @if ($complimentary->isNotEmpty())  
                                            <span class="summary-item-options">  
                                                {{ $complimentary->pluck('name')->join(', ') }}  
                                            </span>  
                                        @endif

                                        @if ($extras->isNotEmpty())  
                                            <span class="summary-item-options">  
                                                @foreach ($extras as $extra)  
                                                    {{ $extra->name }} <span class="summary-item-extra-price">+£{{ number_format($extra->price_delta, 2) }}</span>@if (! $loop->last), @endif  
                                                @endforeach  
                                            </span>  
                                        @endif  
                                    @endif  
                                </div>

                                <div class="summary-item-price">  
                                    £{{ number_format($cartItem->line_total, 2) }}  
                                </div>

                            </div>

                        @endforeach

                        <div class="summary-totals">

                            <div class="summary-totals-row">  
                                <span>Subtotal</span>  
                                <span>£{{ number_format($cartSubtotal, 2) }}</span>  
                            </div>

                            <div class="summary-totals-row muted-value" id="summary-fulfilment-row">  
                                <span>Delivery Fee</span>  
                                <span>Processing..... </span>  
                            </div>

                        </div>

                        <div class="summary-total-final">  
                            <span>Total</span>  
                            <span class="amount">£{{ number_format($cartSubtotal, 2) }}</span>  
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>  
</div>

<script src="{{ asset('storefront/assets/js/vendor.min.js') }}"></script>  
<script src="{{ asset('storefront/assets/js/plugins.min.js') }}"></script>

<script>  
(function ($) {  
    "use strict";

    const $toggle = $('#order-summary-toggle');  
    const $panel = $('#order-summary-collapse-wrap');
    const $continueBtn = $('#checkout-continue-btn');
    const $zipcode = $('#checkout-zipcode');  
    const $zipcodeResult = $('#checkout-zipcode-result');  
    const $deliveryFields = $('#delivery-fields');  
    const $pickupFields = $('#pickup-fields');  
    const $summaryFulfilmentRow = $('#summary-fulfilment-row');
    const $totalAmountDisplay = $('.summary-total-final .amount');
    const $mobileSummaryTotal = $('.mobile-summary-total');

    const cartSubtotal = parseFloat("{{ (float) $cartSubtotal }}");
    const ukPostcodeRegex = /^[A-Z]{1,2}\d[A-Z\d]?\s*\d[A-Z]{2}$/i;
    
    let deliveryAvailable = true;
    let currentDeliveryFee = null;

    $toggle.on('click keypress', function (e) {  
        if (e.type === 'keypress' && e.which !== 13 && e.which !== 32) {  
            return;  
        }

        const expanded = $toggle.attr('aria-expanded') === 'true';

        $toggle.attr('aria-expanded', String(!expanded));  
        $panel.toggleClass('show', !expanded);  
    });

    function showFieldError($field, $error, message) {  
        $field.removeClass('border-color');  
        void $field[0].offsetWidth; // force reflow
        $field.addClass('border-color');

        $error.text(message).addClass('show');

        $field.one('input change', function () {  
            $field.removeClass('border-color');  
            $error.removeClass('show');  
        });  
    }

    function isPickup() {  
        return $('input[name="fulfilment-method"]:checked').val() === 'pickup';  
    }

    function updateSummaryTotals() {
        let finalTotal = cartSubtotal;

        if (isPickup()) {
            $summaryFulfilmentRow.html('<span>Pickup</span><span>Free</span>');
        } else {
            if (currentDeliveryFee !== null && !isNaN(currentDeliveryFee) && deliveryAvailable) {
                finalTotal += currentDeliveryFee;
                $summaryFulfilmentRow.html(`<span>Delivery Fee</span><span>£${currentDeliveryFee.toFixed(2)}</span>`);
            } else {
                $summaryFulfilmentRow.html('<span>Delivery Fee</span><span>Calculated at next step</span>');  
            }
        }

        $totalAmountDisplay.text('£' + finalTotal.toFixed(2));
        $mobileSummaryTotal.text('£' + finalTotal.toFixed(2));
    }

    function applyFulfilmentMethod() {
        const pickup = isPickup();

        $deliveryFields.toggle(!pickup);  
        $pickupFields.toggle(pickup);

        if (pickup) {  
            $continueBtn.prop('disabled', false);
        } else {  
            if (!deliveryAvailable) {
                $continueBtn.prop('disabled', true);
            }
        }

        updateSummaryTotals();

        $deliveryFields.add($pickupFields).find('.field-error').removeClass('show');  
        $deliveryFields.add($pickupFields).find('.border-color').removeClass('border-color');  
    }

    $('input[name="fulfilment-method"]').on('change', applyFulfilmentMethod);  
    applyFulfilmentMethod();

    /* =========================================================  
        INLINE DELIVERY-AREA & DYNAMIC FEE CHECK  
    ========================================================== */

    function showZipcodeResult(message, state) {  
        $zipcodeResult  
            .text(message)  
            .removeClass('is-available is-unavailable is-checking')  
            .addClass('show ' + state);  
    }

    function clearZipcodeResult() {  
        $zipcodeResult.removeClass('show is-available is-unavailable is-checking').text('');  
    }

    function checkPostcode() {
        const postcode = $zipcode.val().trim();

        if (postcode === '' || !ukPostcodeRegex.test(postcode)) {  
            clearZipcodeResult();  
            currentDeliveryFee = null;
            updateSummaryTotals();
            return;  
        }

        showZipcodeResult('Checking delivery availability…', 'is-checking');

        fetch('{{ route('delivery-check.check') }}', {  
            method: 'POST',  
            headers: {  
                'Content-Type': 'application/json',  
                'Accept': 'application/json',  
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),  
            },  
            body: JSON.stringify({ postcode }),  
        })  
        .then(res => res.json())  
        .then(data => {  
            deliveryAvailable = !!data.available;
            
            const parsedFee = parseFloat(data.fee);
            if (deliveryAvailable && !isNaN(parsedFee)) {
                currentDeliveryFee = parsedFee;
            } else {
                currentDeliveryFee = null;
            }

            const message = data.message || (deliveryAvailable ? 'Delivery available' : 'Out of delivery area');
            showZipcodeResult(message, deliveryAvailable ? 'is-available' : 'is-unavailable');  

            $continueBtn.prop('disabled', !isPickup() && !deliveryAvailable);
            updateSummaryTotals();
        })  
        .catch(() => {  
            clearZipcodeResult();  
            deliveryAvailable = true;
            currentDeliveryFee = null;
            $continueBtn.prop('disabled', false);
            updateSummaryTotals();
        });  
    }

    $zipcode.on('blur change', checkPostcode);

    /* =========================================================  
        CONTINUE BUTTON SUBMIT GUARD  
    ========================================================== */

    $continueBtn.on('click', function (e) {
        if (!isPickup() && !deliveryAvailable) {
            e.preventDefault();
            showFieldError($zipcode, $('#checkout-zipcode-error'), 'Sorry, we do not deliver to this postcode.');
            return false;
        }

        // Add your form validation / submission logic here
    });

})(jQuery);
</script>
</body>
</html>