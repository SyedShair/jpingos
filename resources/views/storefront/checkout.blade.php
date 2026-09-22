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
           FULFILMENT METHOD (Delivery / Pickup / Pre-Order)
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

        .checkout-page .fulfilment-option input:focus-visible + label {
            outline: 2px solid var(--green);
            outline-offset: 2px;
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
           PRE-ORDER FIELDS
        ========================================================== */

        .checkout-page .preorder-note {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #fdf6e9;
            border: 1px solid #f0dca3;
            border-radius: 8px;
            padding: 14px 16px;
            font-size: 13.5px;
            color: #7a5c12;
            margin-bottom: 20px;
        }

        .checkout-page .preorder-note i {
            color: #c99a1f;
            margin-top: 2px;
        }

        /* =========================================================
           PAYMENT METHOD
        ========================================================== */

        .checkout-page .payment-toggle {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }

        .checkout-page .payment-option {
            flex: 1;
            position: relative;
        }

        .checkout-page .payment-option input {
            position: absolute;
            opacity: 0;
            inset: 0;
            margin: 0;
            cursor: pointer;
        }

        .checkout-page .payment-option input:disabled {
            cursor: not-allowed;
        }

        .checkout-page .payment-option label {
            display: flex;
            flex-direction: column;
            gap: 2px;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 14px 16px;
            cursor: pointer;
            transition: border-color .2s ease, background .2s ease, opacity .2s ease;
        }

        .checkout-page .payment-option label .payment-option-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            font-size: 14px;
            color: var(--charcoal);
        }

        .checkout-page .payment-option label .payment-option-title i {
            font-size: 16px;
            color: var(--muted);
        }

        .checkout-page .payment-option label .payment-option-sub {
            font-size: 12px;
            color: var(--muted);
            margin-left: 26px;
        }

        .checkout-page .payment-option input:checked + label {
            border-color: var(--green);
            background: #f0f9f4;
        }

        .checkout-page .payment-option input:checked + label .payment-option-title i {
            color: var(--green);
        }

        .checkout-page .payment-option input:disabled + label {
            opacity: .55;
            background: #f7f7f8;
        }

        .checkout-page .payment-option input:focus-visible + label {
            outline: 2px solid var(--green);
            outline-offset: 2px;
        }

        .checkout-page .payment-option-badge {
            display: inline-block;
            background: var(--line);
            color: var(--muted);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            padding: 2px 8px;
            border-radius: 20px;
            margin-left: auto;
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

        /* =========================================================
           FULL-SCREEN PRELOADER
        ========================================================== */

        .page-preloader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 18px;
            padding: 24px;
            text-align: center;
            background: rgba(255, 255, 255, .55);
            -webkit-backdrop-filter: blur(8px) saturate(120%);
            backdrop-filter: blur(8px) saturate(120%);
            opacity: 0;
            transition: opacity .25s ease;
        }

        @supports not ((backdrop-filter: blur(8px)) or (-webkit-backdrop-filter: blur(8px))) {
            .page-preloader { background: rgba(255, 255, 255, .92); }
        }

        .page-preloader.show {
            display: flex;
            opacity: 1;
        }

        @keyframes pp-spin { to { transform: rotate(360deg); } }

        .page-preloader .pp-spinner {
            width: 54px;
            height: 54px;
            border: 4px solid rgba(26, 156, 92, .18);
            border-top-color: #1a9c5c;
            border-radius: 50%;
            animation: pp-spin .8s linear infinite;
        }

        .page-preloader .pp-text {
            font-size: 16px;
            font-weight: 700;
            color: #1A1A1A;
        }

        .page-preloader .pp-sub {
            font-size: 13px;
            color: #6b7280;
            margin-top: -10px;
        }

        body.preloader-open {
            overflow: hidden;
        }

        @media (prefers-reduced-motion: reduce) {
            .page-preloader .pp-spinner { animation-duration: 2.4s; }
            .page-preloader { transition: none; }
        }
    </style>
</head>
<body>

<div class="section checkout-page">
    <div class="container">

     

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
                                <a href="{{ route('storefront.logout') }}">Log out</a>
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

                        <div class="fulfilment-option">
                            <input type="radio" name="fulfilment-method" id="fulfilment-preorder" value="preorder">
                            <label for="fulfilment-preorder">
                                <i class="fa fa-calendar"></i>
                                Pre-Order
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

                    {{-- =================================================
                         PRE-ORDER FIELDS
                    ================================================== --}}

                    <div id="preorder-fields" style="display: none;">

                        <div class="preorder-note">
                            <i class="fa fa-info-circle"></i>
                            <span>Schedule your order for a future date and time.</span>
                        </div>

                        <div class="mb-3">
                            <label for="preorder-type" class="field-hint" style="display:block; font-style: normal; font-weight: 600; color: var(--charcoal);">How would you like it?</label>
                            <select class="form-select" id="preorder-type">
                                <option value="dining">Dining (Reservation)</option>
                                <option value="pickup">Pickup</option>
                                <!-- <option value="delivery">Delivery</option> -->
                            </select>
                        </div>

                        <div class="form-row">
                            <div>
                                <input type="date" id="preorder-date" class="form-control" placeholder="Date">
                                <div class="field-error" id="preorder-date-error"></div>
                            </div>
                            <div>
                                <input type="time" id="preorder-time" class="form-control" placeholder="Time">
                                <div class="field-error" id="preorder-time-error"></div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div>
                                <input type="text" id="checkout-first-name-preorder" class="form-control" placeholder="First Name">
                                <div class="field-error" id="checkout-first-name-preorder-error"></div>
                            </div>
                            <div>
                                <input type="text" id="checkout-last-name-preorder" class="form-control" placeholder="Last Name">
                                <div class="field-error" id="checkout-last-name-preorder-error"></div>
                            </div>
                        </div>

                        <div id="preorder-delivery-address" style="display: none;">

                            <div class="form-row">
                                <div>
                                    <select class="form-select" id="preorder-city">
                                        <option value="nottingham">Nottingham</option>
                                    </select>
                                </div>
                                <div>
                                    <input type="text" id="preorder-zipcode" class="form-control" placeholder="Postcode">
                                    <div class="field-error" id="preorder-zipcode-error"></div>
                                </div>
                            </div>

                            <input type="text" id="preorder-address" class="form-control" placeholder="Delivery Address">
                            <div class="field-error" id="preorder-address-error"></div>

                            <input type="text" id="preorder-apartment" class="form-control" placeholder="Apartment, suite, unit etc. (optional)">

                        </div>

                    </div>

                    <input type="text" id="checkout-phone" class="form-control" placeholder="Phone Number">
                    <div class="field-error" id="checkout-phone-error"></div>
                    <div class="field-hint">*Phone number must be like this 07123456789</div>

                    {{-- =================================================
                         PAYMENT METHOD
                         Card is visible but disabled — no gateway wired
                         up yet. Only Cash on Delivery is selectable, and
                         it's checked by default so the field is always
                         valid without the user having to touch it.
                    ================================================== --}}

                    <div class="checkout-section-title">Payment Method</div>

                    <div class="payment-toggle">

                        <div class="payment-option">
                            <input type="radio" name="payment-method" id="payment-cod" value="cod" checked>
                            <label for="payment-cod">
                                <span class="payment-option-title">
                                    <i class="fa fa-money-bill-wave"></i>
                                    Cash on Delivery
                                </span>
                                <span class="payment-option-sub">Pay when your order arrives or is collected</span>
                            </label>
                        </div>

                        <div class="payment-option">
                            <input type="radio" name="payment-method" id="payment-card" value="card" disabled>
                            <label for="payment-card">
                                <span class="payment-option-title">
                                    <i class="fa fa-credit-card"></i>
                                    Card
                                    <span class="payment-option-badge">Coming Soon</span>
                                </span>
                                <span class="payment-option-sub">Online card payment isn't available yet</span>
                            </label>
                        </div>

                    </div>

                    @guest('customer')
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="checkout-save-info">
                            <label class="form-check-label" for="checkout-save-info">
                                Save this information for next time
                            </label>
                        </div>
                    @endguest

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
                                <span>Calculated at next step</span>
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

{{-- =========================================================
     FULL-SCREEN PRELOADER
========================================================== --}}
<div class="page-preloader" id="checkout-preloader" role="status" aria-live="polite" aria-hidden="true">
    <div class="pp-spinner"></div>
    <div class="pp-text" id="checkout-preloader-text">Placing your order…</div>
    <div class="pp-sub">Please don't close or refresh this page.</div>
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
    const $preorderFields = $('#preorder-fields');
    const $preorderType = $('#preorder-type');
    const $preorderDeliveryAddress = $('#preorder-delivery-address');
    const $summaryFulfilmentRow = $('#summary-fulfilment-row');
    const $totalAmountDisplay = $('.summary-total-final .amount');
    const $mobileSummaryTotal = $('.mobile-summary-total');

    const $preloader = $('#checkout-preloader');
    const $preloaderText = $('#checkout-preloader-text');

    const cartSubtotal = parseFloat("{{ (float) $cartSubtotal }}");
    const ukPostcodeRegex = /^[A-Z]{1,2}\d[A-Z\d]?\s*\d[A-Z]{2}$/i;

    let deliveryAvailable = true;
    let currentDeliveryFee = null;

    // --- Delivery postcode check state (declared up here so every function below can use it) ---
    let lastCheckedPostcode = null;   // postcode we last got an answer for
    let pendingPostcode = null;       // postcode being checked right now
    let pendingPromise = null;
    let checkSeq = 0;                 // ignores out-of-date responses
    let verifyOnce = false;           // stops the Continue click re-checking in a loop

    const normalisePostcode = pc => pc.replace(/\s+/g, '').toUpperCase();

    /* =========================================================
        PRELOADER
    ========================================================== */

    function showPreloader(message) {
        if (message) {
            $preloaderText.text(message);
        }
        $preloader.addClass('show').attr('aria-hidden', 'false');
        $('body').addClass('preloader-open');
    }

    function hidePreloader() {
        $preloader.removeClass('show').attr('aria-hidden', 'true');
        $('body').removeClass('preloader-open');
    }

    $(window).on('pageshow', function (e) {
        if (e.originalEvent && e.originalEvent.persisted) {
            hidePreloader();
            $continueBtn.prop('disabled', false).text('Continue to Payment');
        }
    });

    (function setMinPreorderDate() {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        $('#preorder-date').attr('min', `${yyyy}-${mm}-${dd}`);
    })();

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

    function fulfilmentMethod() {
        return $('input[name="fulfilment-method"]:checked').val();
    }

    function isPickup() {
        return fulfilmentMethod() === 'pickup';
    }

    function isPreOrder() {
        return fulfilmentMethod() === 'preorder';
    }

    function updateSummaryTotals() {
        let finalTotal = cartSubtotal;

        if (isPreOrder()) {
            const label = $preorderType.val() === 'delivery' ? 'Pre-Order (Delivery)'
                : $preorderType.val() === 'pickup' ? 'Pre-Order (Pickup)'
                : 'Pre-Order (Dining)';
            $summaryFulfilmentRow.html(`<span>${label}</span><span>Scheduled</span>`);
        } else if (isPickup()) {
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
        const method = fulfilmentMethod();

        $deliveryFields.toggle(method === 'delivery');
        $pickupFields.toggle(method === 'pickup');
        $preorderFields.toggle(method === 'preorder');

        if (method === 'delivery') {
            $continueBtn.prop('disabled', !deliveryAvailable);
        } else {
            $continueBtn.prop('disabled', false);
        }

        updateSummaryTotals();

        $deliveryFields.add($pickupFields).add($preorderFields).find('.field-error').removeClass('show');
        $deliveryFields.add($pickupFields).add($preorderFields).find('.border-color').removeClass('border-color');
    }

    function applyPreorderType() {
        $preorderDeliveryAddress.toggle($preorderType.val() === 'delivery');
        updateSummaryTotals();
    }

    $('input[name="fulfilment-method"]').on('change', function () {
        applyFulfilmentMethod();

        // Customer switched back to Delivery: re-check whatever postcode is in the box
        if (fulfilmentMethod() === 'delivery') {
            checkPostcode();
        }
    });
    $preorderType.on('change', applyPreorderType);
    applyFulfilmentMethod();
    applyPreorderType();

    /* =========================================================
        INLINE DELIVERY-AREA & DYNAMIC FEE CHECK (normal Delivery only)
        Runs when the customer types, leaves the field, pastes,
        uses browser autofill, OR when the postcode is pre-filled
        from their saved account — and again just before the order
        is placed if it hasn't been checked yet.
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
        const key = normalisePostcode(postcode);

        // Empty or not a valid UK postcode yet: reset, so the button is never left disabled
        if (postcode === '' || !ukPostcodeRegex.test(postcode)) {
            checkSeq++;
            lastCheckedPostcode = null;
            pendingPostcode = null;
            pendingPromise = null;
            deliveryAvailable = true;
            currentDeliveryFee = null;
            clearZipcodeResult();
            $continueBtn.prop('disabled', false);
            updateSummaryTotals();
            return Promise.resolve();
        }

        // Already answered for this postcode, or already asking about it
        if (key === lastCheckedPostcode) {
            return Promise.resolve();
        }
        if (key === pendingPostcode && pendingPromise) {
            return pendingPromise;
        }

        const seq = ++checkSeq;
        pendingPostcode = key;
        showZipcodeResult('Checking delivery availability…', 'is-checking');

        pendingPromise = fetch('{{ route('delivery-check.check') }}', {
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
            if (seq !== checkSeq) return;          // a newer check replaced this one

            pendingPostcode = null;
            pendingPromise = null;
            lastCheckedPostcode = key;
            deliveryAvailable = !!data.available;

            const parsedFee = parseFloat(data.fee);
            currentDeliveryFee = (deliveryAvailable && !isNaN(parsedFee)) ? parsedFee : null;

            const message = data.message || (deliveryAvailable ? 'Delivery available' : 'Out of delivery area');
            showZipcodeResult(message, deliveryAvailable ? 'is-available' : 'is-unavailable');

            $continueBtn.prop('disabled', fulfilmentMethod() === 'delivery' && !deliveryAvailable);
            updateSummaryTotals();
        })
        .catch(() => {
            if (seq !== checkSeq) return;

            pendingPostcode = null;
            pendingPromise = null;
            lastCheckedPostcode = null;
            clearZipcodeResult();
            deliveryAvailable = true;
            currentDeliveryFee = null;
            $continueBtn.prop('disabled', false);
            updateSummaryTotals();
        });

        return pendingPromise;
    }

    // Typing, pasting and browser autofill
    let zipTimer;
    $zipcode.on('input', function () {
        clearTimeout(zipTimer);
        zipTimer = setTimeout(checkPostcode, 500);
    });
    $zipcode.on('blur change', checkPostcode);

    // Saved-account or autofilled postcode: check as soon as the page opens
    function checkPrefilledPostcode() {
        if (fulfilmentMethod() === 'delivery' && $zipcode.val().trim() !== '') {
            checkPostcode();
        }
    }
    checkPrefilledPostcode();
    $(window).on('load', checkPrefilledPostcode);
    setTimeout(checkPrefilledPostcode, 800);       // autofill can land after the page loads

    /* =========================================================
        CONTINUE BUTTON SUBMIT GUARD
    ========================================================== */

    $continueBtn.on('click', function (e) {

        // Delivery: make sure the postcode in the box (typed OR pre-filled) has really been checked
        if (fulfilmentMethod() === 'delivery') {
            const typed = $zipcode.val().trim();

            if (ukPostcodeRegex.test(typed) && normalisePostcode(typed) !== lastCheckedPostcode && !verifyOnce) {
                e.preventDefault();
                verifyOnce = true;
                $continueBtn.prop('disabled', true).text('Checking postcode…');

                checkPostcode().then(function () {
                    $continueBtn.prop('disabled', false).text('Continue to Payment');
                    $continueBtn.trigger('click');          // now runs with the real result
                    if (!deliveryAvailable) {
                        $continueBtn.prop('disabled', true);
                    }
                });

                return false;
            }
            verifyOnce = false;
        }

        if (fulfilmentMethod() === 'delivery' && !deliveryAvailable) {
            e.preventDefault();
            showFieldError($zipcode, $('#checkout-zipcode-error'), 'Sorry, we do not deliver to this postcode.');
            return false;
        }

        let hasError = false;
        const method = fulfilmentMethod();
        const preorder = method === 'preorder';
        const pickup = method === 'pickup';
        const preorderSubtype = preorder ? $preorderType.val() : null;

        const email = $('#checkout-email').val().trim();
        const phone = $('#checkout-phone').val().trim();
        const paymentMethod = $('input[name="payment-method"]:checked').val() || 'cod';

        const firstName = preorder
            ? $('#checkout-first-name-preorder').val().trim()
            : pickup
                ? $('#checkout-first-name-pickup').val().trim()
                : $('#checkout-first-name').val().trim();
        const lastName = preorder
            ? $('#checkout-last-name-preorder').val().trim()
            : pickup
                ? $('#checkout-last-name-pickup').val().trim()
                : $('#checkout-last-name').val().trim();

        if (email === '') {
            showFieldError($('#checkout-email'), $('#checkout-email-error'), 'Please Enter Email');
            hasError = true;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showFieldError($('#checkout-email'), $('#checkout-email-error'), 'Please Enter a Valid Email');
            hasError = true;
        }

        if (preorder) {

            if (firstName === '') {
                showFieldError($('#checkout-first-name-preorder'), $('#checkout-first-name-preorder-error'), 'Please Enter First Name');
                hasError = true;
            }
            if (lastName === '') {
                showFieldError($('#checkout-last-name-preorder'), $('#checkout-last-name-preorder-error'), 'Please Enter Last Name');
                hasError = true;
            }

            const preorderDate = $('#preorder-date').val();
            const preorderTime = $('#preorder-time').val();

            if (preorderDate === '') {
                showFieldError($('#preorder-date'), $('#preorder-date-error'), 'Please choose a date');
                hasError = true;
            }
            if (preorderTime === '') {
                showFieldError($('#preorder-time'), $('#preorder-time-error'), 'Please choose a time');
                hasError = true;
            }

            if (preorderSubtype === 'delivery') {
                const preorderAddress = $('#preorder-address').val().trim();
                const preorderZipcode = $('#preorder-zipcode').val().trim();

                if (preorderAddress === '') {
                    showFieldError($('#preorder-address'), $('#preorder-address-error'), 'Please Enter Address');
                    hasError = true;
                }
                if (preorderZipcode === '') {
                    showFieldError($('#preorder-zipcode'), $('#preorder-zipcode-error'), 'Please Enter Postcode');
                    hasError = true;
                } else if (!ukPostcodeRegex.test(preorderZipcode)) {
                    showFieldError($('#preorder-zipcode'), $('#preorder-zipcode-error'), 'Please Enter a Valid UK Postcode');
                    hasError = true;
                }
            }

        } else if (pickup) {
            if (firstName === '') {
                showFieldError($('#checkout-first-name-pickup'), $('#checkout-first-name-pickup-error'), 'Please Enter First Name');
                hasError = true;
            }
            if (lastName === '') {
                showFieldError($('#checkout-last-name-pickup'), $('#checkout-last-name-pickup-error'), 'Please Enter Last Name');
                hasError = true;
            }
        } else {
            if (firstName === '') {
                showFieldError($('#checkout-first-name'), $('#checkout-first-name-error'), 'Please Enter First Name');
                hasError = true;
            }
            if (lastName === '') {
                showFieldError($('#checkout-last-name'), $('#checkout-last-name-error'), 'Please Enter Last Name');
                hasError = true;
            }

            const address = $('#checkout-address').val().trim();
            const zipcode = $zipcode.val().trim();

            if (address === '') {
                showFieldError($('#checkout-address'), $('#checkout-address-error'), 'Please Enter Address');
                hasError = true;
            }

            if (zipcode === '') {
                showFieldError($zipcode, $('#checkout-zipcode-error'), 'Please Enter Postcode');
                hasError = true;
            } else if (!ukPostcodeRegex.test(zipcode)) {
                showFieldError($zipcode, $('#checkout-zipcode-error'), 'Please Enter a Valid UK Postcode');
                hasError = true;
            }
        }

        if (phone === '') {
            showFieldError($('#checkout-phone'), $('#checkout-phone-error'), 'Please Enter Mobile Number');
            hasError = true;
        } else if (!/^\d{11}$/.test(phone)) {
            showFieldError($('#checkout-phone'), $('#checkout-phone-error'), 'Mobile number must be 11 digits');
            hasError = true;
        } else if (!/^0\d{10}$/.test(phone)) {
            showFieldError($('#checkout-phone'), $('#checkout-phone-error'), 'Mobile number format is invalid');
            hasError = true;
        }

        if (hasError) {
            return;
        }

        const payload = {
            first_name: firstName,
            last_name: lastName,
            email: email,
            phone: phone,
            is_pre_order: preorder,
            pre_order_date: preorder ? $('#preorder-date').val() : null,
            pre_order_time: preorder ? $('#preorder-time').val() : null,
            payment_method: paymentMethod,
        };

        if (preorder) {
            payload.order_type = preorderSubtype;

            if (preorderSubtype === 'delivery') {
                payload.address = $('#preorder-address').val().trim();
                payload.apartment = $('#preorder-apartment').val().trim() || null;
                payload.city = $('#preorder-city option:selected').text();
                payload.postcode = $('#preorder-zipcode').val().trim();
                payload.notes = null;
                payload.delivery_fee = 0;
            } else {
                payload.address = null;
                payload.apartment = null;
                payload.city = null;
                payload.postcode = null;
                payload.notes = null;
                payload.delivery_fee = 0;
            }
        } else if (pickup) {
            payload.order_type = 'pickup';
            payload.address = null;
            payload.apartment = null;
            payload.city = null;
            payload.postcode = null;
            payload.notes = null;
            payload.delivery_fee = 0;
        } else {
            payload.order_type = 'delivery';
            payload.address = $('#checkout-address').val().trim();
            payload.apartment = $('#checkout-apartment').val().trim() || null;
            payload.city = $('#checkout-city option:selected').text();
            payload.postcode = $zipcode.val().trim();
            payload.notes = $('#checkout-delivery-notes').val().trim() || null;
            payload.delivery_fee = (currentDeliveryFee !== null && !isNaN(currentDeliveryFee)) ? currentDeliveryFee : 0;
        }

        const originalLabel = $continueBtn.text();
        $continueBtn.prop('disabled', true).text('Please wait...');

        showPreloader(
            preorder
                ? (preorderSubtype === 'dining' ? 'Booking your table…' : 'Scheduling your pre-order…')
                : pickup
                    ? 'Confirming your pickup order…'
                    : 'Placing your delivery order…'
        );

        fetch('{{ route('storefront.checkout.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify(payload)
        })
        .then(function (response) {
            if (!response.ok) {
                return response.json().then(function (err) {
                    throw new Error(err.message || 'Something went wrong placing your order.');
                });
            }
            return response.json();
        })
        .then(function (data) {
            $preloaderText.text('Order confirmed — taking you to your receipt…');
            window.location.href = data.confirmation_url;
        })
        .catch(function (error) {
            hidePreloader();
            alert(error.message);
            $continueBtn.prop('disabled', false).text(originalLabel);
        });
    });

})(jQuery);
</script>
</body>
</html>