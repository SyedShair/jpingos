@extends('storefront.layouts.app')

@section('title', 'Deals & Offers | ' . config('app.name', 'Restaurant'))

@push('styles')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>

    /* =========================================================
       DEALS PAGE
    ========================================================== */

    .deals-list-page {
        --ember: #D12026;
        --ember-dark: #A5171C;
        --charcoal: #1A1A1A;
        --cream: #FAF5EE;
        --gold: #D9A441;
        --line: #EAE0D3;
        --muted: #8B7F73;
        --green: #1a9c5c;
    }


    /* =========================================================
       TOOLBAR
    ========================================================== */

    .deals-list-page .deals-toolbar-panel {
        background: #f7f7f8;
        border: 1px solid #ececec;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 40px;
    }

    .deals-list-page .deals-toolbar-count {
        color: #6b7280;
        font-size: 14px;
        margin-bottom: 12px;
    }

    .deals-list-page .deals-toolbar-form {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: stretch;
    }

    .deals-list-page .deals-toolbar-form .toolbar-search,
    .deals-list-page .deals-toolbar-form select.toolbar-select {
        background: #fff;
        border: 1px solid #e3e3e6;
        border-radius: 8px;
        height: 48px;
        padding: 0 16px;
        font-size: 14px;
        color: #333;
    }

    .deals-list-page .deals-toolbar-form .toolbar-search {
        flex: 2 1 220px;
    }

    .deals-list-page .deals-toolbar-form select.toolbar-select {
        flex: 1 1 150px;
    }

    .deals-list-page .deals-toolbar-form .toolbar-submit {
        flex: 0 0 auto;
        background: var(--charcoal);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0 28px;
        font-weight: 700;
        letter-spacing: .02em;
        text-transform: uppercase;
        font-size: 13px;
        transition: background .2s ease;
    }

    .deals-list-page .deals-toolbar-form .toolbar-submit:hover {
        background: #000;
    }

    .deals-list-page .shop_toolbar_btn {
        display: flex;
        gap: 8px;
        margin-left: auto;
    }

    .deals-list-page .shop_toolbar_btn button {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 1px solid #e3e3e6;
        background: #fff;
        color: #6b7280 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .2s ease;
    }

    .deals-list-page .shop_toolbar_btn button.active,
    .deals-list-page .shop_toolbar_btn button:hover {
        background: var(--charcoal);
        border-color: var(--charcoal);
        color: #fff !important;
    }


    /* =========================================================
       DEAL CARD
    ========================================================== */

    .deals-list-page .deal-row {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 12px;
        overflow: hidden;
        height: 100%;
        transition:
            box-shadow .25s ease,
            transform .25s ease,
            border-color .25s ease;
    }

    .deals-list-page .deal-row:hover {
        box-shadow: 0 10px 28px rgba(0, 0, 0, .09);
        transform: translateY(-2px);
        border-color: #ddd0c0;
    }

    .deals-list-page .deal-row .product-inner {
        display: flex;
        height: 100%;
    }


    /* =========================================================
       IMAGE
    ========================================================== */

    .deals-list-page .deal-row .thumb {
        position: relative;
        background: #f2ede6;
        flex-shrink: 0;
        overflow: hidden;
    }

    .deals-list-page .deal-row .thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .35s ease;
    }

    .deals-list-page .deal-row:hover .thumb img {
        transform: scale(1.04);
    }


    /* =========================================================
       ICON TILE
    ========================================================== */

    .deals-list-page .deal-row .thumb.icon-tile {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .deals-list-page .deal-row .thumb.icon-tile.tone-ember {
        background: linear-gradient(
            135deg,
            var(--ember),
            var(--ember-dark)
        );
    }

    .deals-list-page .deal-row .thumb.icon-tile.tone-gold {
        background: linear-gradient(
            135deg,
            var(--gold),
            #b9863a
        );
    }

    .deals-list-page .deal-row .thumb.icon-tile i {
        color: rgba(255, 255, 255, .95);
    }


    /* =========================================================
       BADGES
    ========================================================== */

    .deals-list-page .deal-row .badges {
        position: absolute;
        top: 12px;
        left: 12px;
        z-index: 3;
    }

    .deals-list-page .deal-row .badges .sale {
        display: inline-block;
        background: var(--green);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        letter-spacing: .02em;
        padding: 6px 12px;
        border-radius: 6px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, .12);
    }


    /* =========================================================
       QUICK VIEW / IMAGE ACTION
    ========================================================== */

    .deals-list-page .deal-row .thumb .actions {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 5;

        background: rgba(0, 0, 0, .18);

        opacity: 0;
        visibility: hidden;

        transition:
            opacity .25s ease,
            visibility .25s ease;
    }

    .deals-list-page .deal-row .thumb:hover .actions {
        opacity: 1;
        visibility: visible;
    }

    .deals-list-page .deal-row .thumb .actions .quickview {
        width: 50px;
        height: 50px;

        display: flex;
        align-items: center;
        justify-content: center;

        background: #fff;
        color: #1a1a1a;

        border-radius: 50%;

        font-size: 22px;
        line-height: 1;

        text-decoration: none;

        box-shadow: 0 6px 18px rgba(0, 0, 0, .20);

        transform: scale(.75);

        transition:
            transform .25s ease,
            background .25s ease,
            color .25s ease;
    }

    .deals-list-page .deal-row .thumb:hover .quickview {
        transform: scale(1);
    }

    .deals-list-page .deal-row .thumb .actions .quickview:hover {
        background: var(--ember);
        color: #fff;
    }


    /* =========================================================
       CONTENT
    ========================================================== */

    .deals-list-page .deal-row .content {
        padding: 18px 20px;
    }

    .deals-list-page .deal-row .content .type-label {
        display: block;
        color: var(--muted);
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .07em;
        margin-bottom: 6px;
    }

    .deals-list-page .deal-row .content h5.title {
        color: var(--charcoal);
        font-family: 'Fraunces', serif;
        font-size: 17px;
        font-weight: 700;
        text-transform: uppercase;
        line-height: 1.3;
        margin-bottom: 8px;
    }

    .deals-list-page .deal-row .content h5.title a {
        color: inherit;
        text-decoration: none;
    }

    .deals-list-page .deal-row .content h5.title a:hover {
        color: var(--ember-dark);
    }

    .deals-list-page .deal-row .content .desc-line {
        color: var(--muted);
        font-size: 14px;
        line-height: 1.55;
        margin-bottom: 10px;
    }

    .deals-list-page .deal-row .content .ratings {
        display: block;
        margin-bottom: 8px;
        color: #f5a623;
        font-size: 13px;
    }

    .deals-list-page .deal-row .content .ratings .rating-num {
        color: #9ca3af;
        margin-left: 4px;
    }


    /* =========================================================
       PRICE
    ========================================================== */

    .deals-list-page .deal-row .content .price {
        display: block;
        margin-top: 6px;
        margin-bottom: 6px;
    }

    .deals-list-page .deal-row .content .price .new {
        color: var(--green);
        font-family: 'Fraunces', serif;
        font-size: 19px;
        font-weight: 700;
        margin-right: 8px;
    }

    .deals-list-page .deal-row .content .price .old {
        color: #9ca3af;
        text-decoration: line-through;
        font-size: 14px;
        font-weight: 400;
    }


    /* =========================================================
       PROMO CODE
    ========================================================== */

    .deals-list-page .deal-row .content .font-monospace {
        display: inline-block;
        background: var(--cream);
        border: 1px dashed var(--gold);
        border-radius: 8px;
        padding: 4px 10px;
        font-size: 13px;
    }


    /* =========================================================
       BUTTON
    ========================================================== */

    .deals-list-page .deal-row .shop-list-btn {
        margin-top: 13px;
    }

    .deals-list-page .deal-row .shop-list-btn .btn {
        border-radius: 8px;
        border-color: var(--charcoal);
        color: var(--charcoal);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: .02em;

        transition:
            background .2s ease,
            color .2s ease,
            border-color .2s ease;
    }

    .deals-list-page .deal-row .shop-list-btn .btn:hover {
        background: var(--charcoal);
        border-color: var(--charcoal);
        color: #fff;
    }


    /* =========================================================
       COUNTDOWN
    ========================================================== */

    .deals-list-page .countdown-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 10px;
    }

    .deals-list-page .single-countdown {
        min-width: 44px;
        padding: 6px 8px;

        background: var(--charcoal);
        color: #fff;

        border-radius: 8px;
        text-align: center;
    }

    .deals-list-page .single-countdown_time {
        display: block;
        color: var(--gold);
        font-family: 'Fraunces', serif;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.1;
    }

    .deals-list-page .single-countdown_text {
        display: block;
        color: #cbb9a9;
        font-size: 8.5px;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-top: 2px;
    }


    /* =========================================================
       LIST VIEW
    ========================================================== */

    .deals-list-page .deals_wrapper.grid_list .product {
        flex: 0 0 100%;
        max-width: 100%;
        margin-bottom: 20px;
    }

    .deals-list-page .deals_wrapper.grid_list .deal-row .product-inner {
        flex-direction: row;
        align-items: stretch;
    }

    .deals-list-page .deals_wrapper.grid_list .deal-row .thumb {
        flex: 0 0 220px;
        width: 220px;
        max-width: 220px;
        aspect-ratio: 4 / 3;
    }

    .deals-list-page .deals_wrapper.grid_list .deal-row .content {
        flex: 1 1 300px;
    }


    /* =========================================================
       GRID VIEW
    ========================================================== */

    .deals-list-page .deals_wrapper.grid_3 {
        display: flex;
        flex-wrap: wrap;
        margin-left: -12px;
        margin-right: -12px;
    }

    .deals-list-page .deals_wrapper.grid_3 .product {
        flex: 0 0 33.333333%;
        max-width: 33.333333%;

        padding-left: 12px;
        padding-right: 12px;

        margin-bottom: 24px;
    }

    .deals-list-page .deals_wrapper.grid_3 .deal-row .product-inner {
        flex-direction: column;
    }

    .deals-list-page .deals_wrapper.grid_3 .deal-row .thumb {
        width: 100%;
        aspect-ratio: 1 / 1;
    }

    .deals-list-page .deals_wrapper.grid_3 .deal-row .thumb.icon-tile i {
        font-size: 42px;
    }

    .deals-list-page .deals_wrapper.grid_3 .deal-row .content {
        flex: 1;
        display: flex;
        flex-direction: column;
        text-align: center;
    }

    .deals-list-page .deals_wrapper.grid_3 .deal-row .content h5.title {
        font-size: 15px;
    }

    .deals-list-page .deals_wrapper.grid_3 .deal-row .content .desc-line {
        display: none;
    }

    .deals-list-page .deals_wrapper.grid_3 .deal-row .content .ratings {
        justify-content: center;
    }

    .deals-list-page .deals_wrapper.grid_3 .deal-row .countdown-wrapper {
        justify-content: center;
    }

    .deals-list-page .deals_wrapper.grid_3 .deal-row .price {
        margin-top: 5px;
    }

    .deals-list-page .deals_wrapper.grid_3 .deal-row .shop-list-btn {
        margin-top: auto;
        padding-top: 10px;
    }

    .deals-list-page .deals_wrapper.grid_3 .deal-row .shop-list-btn .btn {
        width: 100%;
    }


    /* =========================================================
       SIDEBAR
    ========================================================== */

    .deals-list-page .sidebar_widget .widget_inner {
        background: #fff;
        border: 1px solid #ececec;
        border-radius: 12px;
        padding: 24px;
    }

    .deals-list-page .sidebar_widget .widget-title {
        font-weight: 800;
        font-size: 18px;
        color: var(--charcoal);
    }

    .deals-list-page .sidebar_widget .search-box {
        position: relative;
        display: flex;
    }

    .deals-list-page .sidebar_widget .search-box input {
        padding-right: 50px;
    }

    .deals-list-page .sidebar_widget .search-box button {
        min-width: 45px;
    }

    .deals-list-page .sidebar-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .deals-list-page .sidebar-list li {
        border-bottom: 1px solid #ececec;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

    .deals-list-page .sidebar-list li:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .deals-list-page .sidebar-list a {
        display: block;
        color: #4b5563;
        font-size: 14px;
        font-weight: 600;
        transition: color .2s ease;
    }

    .deals-list-page .sidebar-list a:hover {
        color: var(--ember-dark);
    }

    .deals-list-page .sidebar-list a.active {
        color: var(--green);
        font-weight: 700;
    }

    .deals-list-page .single-product-list {
        border-radius: 10px;
        transition: box-shadow .2s ease, transform .2s ease;
    }

    .deals-list-page .single-product-list:hover {
        box-shadow: 0 6px 16px rgba(0, 0, 0, .06);
        transform: translateY(-2px);
    }


    /* =========================================================
       EMPTY STATE
    ========================================================== */

    .deals-list-page .empty-deals {
        background: var(--cream);
        border: 1px dashed var(--line);
        border-radius: 16px;
        padding: 48px 24px;
        color: var(--muted);
        text-align: center;
        width: 100%;
    }


    /* =========================================================
       RESPONSIVE
    ========================================================== */

    @media (max-width: 991px) {

        .deals-list-page .deals_wrapper.grid_3 .product {
            flex: 0 0 50%;
            max-width: 50%;
        }

    }


    @media (max-width: 767px) {

        .deals-list-page .deals_wrapper.grid_list .deal-row .thumb {
            flex: 0 0 180px;
            width: 180px;
            max-width: 180px;
        }

        .deals-list-page .deal-row .content {
            padding: 15px;
        }

        .deals-list-page .deals-toolbar-form .toolbar-search {
            flex: 1 1 100%;
        }

        .deals-list-page .deals-toolbar-form select.toolbar-select {
            flex: 1 1 45%;
        }

        .deals-list-page .deals-toolbar-form .toolbar-submit {
            flex: 1 1 100%;
        }

        .deals-list-page .shop_toolbar_btn {
            margin-left: 0;
            flex: 1 1 100%;
            justify-content: center;
        }

    }


    @media (max-width: 575px) {

        .deals-list-page .deals_wrapper.grid_3 .product {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .deals-list-page .deals_wrapper.grid_list .deal-row .product-inner {
            flex-direction: column;
        }

        .deals-list-page .deals_wrapper.grid_list .deal-row .thumb {
            flex: 0 0 auto;
            width: 100%;
            max-width: 100%;
            aspect-ratio: 4 / 3;
        }

        .deals-list-page .deals_wrapper.grid_list .deal-row .content {
            flex: 1 1 auto;
        }

    }


    /* =========================================================
       TOOLBAR TOP ROW — count + filter trigger + search toggle
    ========================================================== */

    .deals-list-page .deals-toolbar-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 14px;
    }

    .deals-list-page .deals-toolbar-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .deals-list-page .deals-filter-trigger {
        background: var(--charcoal);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0 16px;
        height: 42px;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: .02em;
    }

    .deals-list-page .deals-search-wrap {
        position: relative;
    }

    .deals-list-page .deals-search-toggle {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: var(--ember);
        color: #fff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        transition: background .2s ease;
    }

    .deals-list-page .deals-search-toggle:hover {
        background: var(--ember-dark);
    }

    .deals-list-page .deals-search-form {
        display: none;
        position: absolute;
        top: 52px;
        right: 0;
        z-index: 30;
        background: #fff;
        border: 1px solid #ececec;
        border-radius: 10px;
        box-shadow: 0 14px 34px rgba(0, 0, 0, .14);
        padding: 10px;
        width: 280px;
    }

    .deals-list-page .deals-search-form.open {
        display: flex;
        gap: 8px;
    }

    .deals-list-page .deals-search-form input {
        flex: 1;
        border: 1px solid #e3e3e6;
        border-radius: 6px;
        padding: 0 12px;
        height: 40px;
        font-size: 14px;
    }

    .deals-list-page .deals-search-form button {
        background: var(--ember);
        color: #fff;
        border: none;
        border-radius: 6px;
        width: 40px;
        height: 40px;
    }


    /* =========================================================
       MOBILE FILTER DRAWER (sidebar becomes a left slide-in panel)
    ========================================================== */

    .deals-list-page .deals-filter-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .55);
        z-index: 1040;
    }

    .deals-list-page .deals-filter-drawer-head button {
        background: none;
        border: none;
        font-size: 18px;
        color: var(--charcoal);
    }

    @media (max-width: 991.98px) {

        .deals-list-page .deals-filter-col {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 300px;
            max-width: 85vw;
            background: #fff;
            z-index: 1050;
            padding: 20px;
            overflow-y: auto;
            transform: translateX(-100%);
            transition: transform .28s ease;
        }

        .deals-list-page .deals-filter-col.open {
            transform: translateX(0);
        }

        .deals-list-page .deals-filter-backdrop.open {
            display: block;
        }
    }


    /* =========================================================
       COUNTDOWN — upgraded, higher-contrast, brand-accent version
    ========================================================== */

    .deals-list-page .countdown-wrapper {
        display: flex;
        gap: 8px;
        margin-bottom: 10px;
    }

    .deals-list-page .single-countdown {
        min-width: 54px;
        padding: 8px 6px 7px;
        background: linear-gradient(160deg, var(--ember), var(--ember-dark));
        color: #fff;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 6px 16px rgba(209, 32, 38, .3);
        position: relative;
        overflow: hidden;
    }

    .deals-list-page .single-countdown::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(255,255,255,.18), rgba(255,255,255,0) 55%);
        pointer-events: none;
    }

    .deals-list-page .single-countdown_time {
        display: block;
        color: #fff;
        font-family: 'Fraunces', serif;
        font-size: 19px;
        font-weight: 700;
        line-height: 1.1;
        font-variant-numeric: tabular-nums;
    }

    .deals-list-page .single-countdown_text {
        display: block;
        color: rgba(255, 255, 255, .85);
        font-size: 9px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .07em;
        margin-top: 3px;
    }

    /* Seconds box pulses once the deal is inside its final hour */
    .deals-list-page .single-countdown.cd-urgent {
        animation: cd-pulse 1s ease-in-out infinite;
    }

    @keyframes cd-pulse {
        0%, 100% { box-shadow: 0 6px 16px rgba(209, 32, 38, .3); }
        50% { box-shadow: 0 6px 22px rgba(209, 32, 38, .65); }
    }

</style>

@endpush


@section('content')

@php
    $orderPhone = config('restaurant.order_phone', '+10123456789');
@endphp


{{-- =========================================================
     BREADCRUMB
========================================================= --}}
<!-- 
<div class="section">

    <div class="breadcrumb-area bg-light">

        <div class="container-fluid">

            <div class="breadcrumb-content text-center">

                <h1 class="title">
                    Deals &amp; Offers
                </h1>

                <ul>

                    <li>
                        <a href="{{ route('storefront.home') }}">
                            Home
                        </a>
                    </li>

                    <li class="active">
                        Deals
                    </li>

                </ul>

            </div>

        </div>

    </div>

</div> -->


{{-- =========================================================
     DEALS SECTION
========================================================= --}}

<div class="section section-margin deals-list-page">

    <div class="container">

        <div class="row flex-row-reverse">


            {{-- =================================================
                 MAIN CONTENT
            ================================================== --}}

            <div class="col-lg-9 col-12 col-custom">


                {{-- =================================================
                     TOOLBAR
                     Same $search/$activeType/$perPage/$sort fields and
                     route as before — search is now a visible input
                     instead of a hidden one, and sort/per-page/search
                     share one row with the grid/list toggle.
                ================================================== --}}

                <div class="deals-toolbar-panel" data-aos="fade-up" data-aos-delay="100">

                    <div class="deals-toolbar-top">

                        <div class="deals-toolbar-count">

                            @if ($deals->total() > 0)

                                Showing
                                {{ $deals->firstItem() }}–{{ $deals->lastItem() }}
                                of
                                {{ $deals->total() }}
                                deals

                            @else

                                No deals found

                            @endif

                        </div>

                        <div class="deals-toolbar-actions">

                            {{-- Mobile-only trigger for the filter drawer (sidebar) --}}
                            <button type="button" class="deals-filter-trigger d-lg-none" id="deals-filter-open">
                                <i class="fa fa-sliders"></i> Filter
                            </button>

                            {{-- Search — collapsed into a corner icon, expands on click
                                 instead of sitting inline as a full-width input --}}
                            <div class="deals-search-wrap">
                                <button type="button" class="deals-search-toggle" id="deals-search-toggle" aria-label="Search deals">
                                    <i class="fa fa-search"></i>
                                </button>

                                <form
                                    method="GET"
                                    action="{{ route('storefront.deals.index') }}"
                                    class="deals-search-form"
                                    id="deals-search-form"
                                >
                                    <input type="hidden" name="type" value="{{ $activeType }}">
                                    <input type="hidden" name="sort" value="{{ $sort }}">
                                    <input type="hidden" name="per_page" value="{{ $perPage }}">

                                    <input
                                        type="text"
                                        name="search"
                                        value="{{ $search }}"
                                        placeholder="Search deals..."
                                        aria-label="Search deals"
                                        autocomplete="off"
                                    >
                                    <button type="submit" aria-label="Submit search"><i class="fa fa-arrow-right"></i></button>
                                </form>
                            </div>

                        </div>

                    </div>

                    <form
                        method="GET"
                        action="{{ route('storefront.deals.index') }}"
                        id="deals-toolbar-form"
                        class="deals-toolbar-form"
                    >

                        <input
                            type="hidden"
                            name="type"
                            value="{{ $activeType }}"
                        >

                        <input type="hidden" name="search" value="{{ $search }}">

                        <select
                            class="toolbar-select"
                            name="sort"
                            aria-label="Sort deals"
                            onchange="this.form.submit()"
                        >
                            <option value="default" @selected($sort === 'default')>Sort by Name</option>
                            <option value="latest" @selected($sort === 'latest')>Sort by Latest</option>
                        </select>

                        <select
                            class="toolbar-select"
                            name="per_page"
                            aria-label="Deals per page"
                            onchange="this.form.submit()"
                        >
                            @foreach ([12, 24, 30] as $option)
                                <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }} / page</option>
                            @endforeach
                        </select>

                    </form>

                </div>


                {{-- =================================================
                     DEALS
                     (unchanged — same @switch/@case per deal type,
                     same @include for add-to-cart, same data-slug /
                     data-deal-id quickview wiring)
                ================================================== --}}

                <div
                    class="deals_wrapper grid_list"
                    id="deals-wrapper"
                    data-aos="fade-up"
                    data-aos-delay="200"
                >

                    @forelse ($deals as $deal)

                        <div class="product">

                            <div class="deal-row">

                                <div class="product-inner">


                                    @php
                                        // All four are referenced in the data-slug
                                        // fallback chain further down regardless of
                                        // which @case runs — initialize them here so
                                        // none of them are ever undefined. This is
                                        // the actual fix: previously only the ONE
                                        // variable each @case computed existed, so
                                        // the other three were undefined vars, which
                                        // Laravel's exception handler turns into a
                                        // thrown ErrorException — i.e. every single
                                        // deal, of every type, was crashing this loop.
                                        $target = null;
                                        $buyItem = null;
                                        $freeItem = null;
                                        $comboFirstItem = null;
                                    @endphp

                                    @switch($deal->type)


                                        {{-- =================================================
                                             FLASH / HAPPY HOUR / LUNCH
                                        ================================================== --}}

                                        @case('flash_deal')
                                        @case('happy_hour')
                                        @case('lunch_special')

                                            @php
                                                $target = $deal->appliesToItems->first()?->menuItem;
                                            @endphp


                                            <div class="thumb">

                                                <a
                                                    href="{{ $target ? route('storefront.dish', $target->slug) : '#' }}"
                                                    class="image"
                                                >

                                                    <img
                                                        src="{{ $deal->image ? Storage::url($deal->image) : asset('storefront/assets/images/products/medium-size/1.jpg') }}"
                                                        alt="{{ $deal->name }}"
                                                    >

                                                </a>


                                                @if ($deal->discountBadgeLabel())

                                                    <span class="badges">

                                                        <span class="sale">
                                                            {{ $deal->discountBadgeLabel() }}
                                                        </span>

                                                    </span>

                                                @endif


                                                {{-- QUICK VIEW --}}

                                                @if ($target)

                                                    <div class="actions">

                                                       <a
    href="javascript:void(0)"
    class="action quickview"
    data-bs-toggle="modal"
    data-bs-target="#exampleModalCenter"
    data-slug="{{ $target?->slug ?? $buyItem?->slug ?? $freeItem?->slug ?? $comboFirstItem?->slug }}"
    data-deal-id="{{ $deal->id }}"
    title="Quick View"
>
    <i class="pe-7s-search"></i>
</a>

                                                    </div>

                                                @endif

                                            </div>


                                            <div class="content">

                                                <span class="type-label">
                                                    {{ $typeLabels[$deal->type] ?? ucfirst(str_replace('_', ' ', $deal->type)) }}
                                                </span>


                                                @if ($deal->isCurrentlyActive() && $deal->countdownTarget())

                                                    <div
                                                        class="countdown-wrapper"
                                                        data-countdown="{{ $deal->countdownTarget()->format('Y/m/d H:i:s') }}"
                                                    ></div>

                                                @else

                                                    <p class="desc-line mb-2">
                                                        {{ $deal->scheduleSummary() }}
                                                    </p>

                                                @endif


                                                <h5 class="title">

                                                    <a
                                                        href="{{ $target ? route('storefront.dish', $target->slug) : '#' }}"
                                                    >
                                                        {{ $deal->name }}
                                                    </a>

                                                </h5>

                                                <span class="ratings">
                                                    <span class="rating-wrap">
                                                        <span class="star" style="width: 100%"></span>
                                                    </span>
                                                    <span class="rating-num">(5)</span>
                                                </span>


                                                @if ($target)

                                                    <p class="desc-line mb-0">
                                                        {{ $target->name }}
                                                    </p>

                                                    <span class="price">
                                                        <span class="new">
                                                            £{{ number_format($deal->discountedPriceFor((float) $target->price), 2) }}
                                                        </span>
                                                        <span class="old">
                                                            £{{ number_format($target->price, 2) }}
                                                        </span>
                                                    </span>

                                                @endif


                                                <div class="shop-list-btn">

                                                    @include('storefront.partials.add-bundle-to-cart-button', ['deal' => $deal, 'menuItem' => $target])

                                                </div>

                                            </div>

                                            @break


                                        {{-- =================================================
                                             TIERED SPEND
                                        ================================================== --}}

                                        @case('tiered_spend')

                                            <div class="thumb icon-tile tone-ember">

                                                <i class="fa fa-percent"></i>

                                            </div>


                                            <div class="content">

                                                <span class="type-label">
                                                    {{ $typeLabels[$deal->type] ?? 'Tiered Spend' }}
                                                </span>

                                                <h5 class="title">
                                                    {{ $deal->name }}
                                                </h5>

                                                <p class="desc-line">

                                                    Spend
                                                    £{{ number_format($deal->min_spend, 2) }}
                                                    or more and get

                                                    @if ($deal->discount_type === 'free_delivery')

                                                        free delivery.

                                                    @else

                                                        £{{ number_format($deal->discount_value, 2) }}
                                                        off your order.

                                                    @endif

                                                </p>


                                                <div class="shop-list-btn">

                                                    <a href="{{ route('storefront.home') }}" class="btn btn-outline-dark btn-hover-primary">
                                                        <i class="fa fa-utensils me-2"></i>
                                                        Browse Menu
                                                    </a>

                                                </div>

                                            </div>

                                            @break


                                        {{-- =================================================
                                             BOGO
                                        ================================================== --}}

                                        @case('bogo')

                                            @php
                                                $buyItem = $deal->buyItems->first()?->menuItem;
                                            @endphp


                                            <div class="thumb">

                                                <a
                                                    href="{{ $buyItem ? route('storefront.dish', $buyItem->slug) : '#' }}"
                                                    class="image"
                                                >

                                                    <img
                                                        src="{{ $deal->image ? Storage::url($deal->image) : asset('storefront/assets/images/products/medium-size/1.jpg') }}"
                                                        alt="{{ $deal->name }}"
                                                    >

                                                </a>


                                                <span class="badges">

                                                    <span class="sale">
                                                        BOGO
                                                    </span>

                                                </span>


                                                @if ($buyItem)

                                                    <div class="actions">

                                                       <a
    href="javascript:void(0)"
    class="action quickview"
    data-bs-toggle="modal"
    data-bs-target="#exampleModalCenter"
    data-slug="{{ $target?->slug ?? $buyItem?->slug ?? $freeItem?->slug ?? $comboFirstItem?->slug }}"
    data-deal-id="{{ $deal->id }}"
    title="Quick View"
>
    <i class="pe-7s-search"></i>
</a>
                                                    </div>

                                                @endif

                                            </div>


                                            <div class="content">

                                                <span class="type-label">
                                                    {{ $typeLabels[$deal->type] ?? 'Buy One Get One' }}
                                                </span>


                                                <h5 class="title">

                                                    <a
                                                        href="{{ $buyItem ? route('storefront.dish', $buyItem->slug) : '#' }}"
                                                    >
                                                        {{ $deal->name }}
                                                    </a>

                                                </h5>

                                                <span class="ratings">
                                                    <span class="rating-wrap">
                                                        <span class="star" style="width: 100%"></span>
                                                    </span>
                                                    <span class="rating-num">(5)</span>
                                                </span>


                                                <p class="desc-line">

                                                    Buy
                                                    {{ $deal->buy_quantity }},
                                                    get
                                                    {{ $deal->get_quantity }}

                                                    {{
                                                        $deal->get_discount_percent >= 100
                                                            ? 'free'
                                                            : $deal->get_discount_percent . '% off'
                                                    }}.

                                                </p>

                                                @if ($buyItem)

                                                    <span class="price">
                                                        <span class="new">
                                                            £{{ number_format((float) $buyItem->price, 2) }}
                                                        </span>
                                                    </span>

                                                @endif


                                                <div class="shop-list-btn">

                                                    @include('storefront.partials.add-bundle-to-cart-button', ['deal' => $deal, 'menuItem' => $buyItem])

                                                </div>

                                            </div>

                                            @break


                                        {{-- =================================================
                                             FREE GIFT
                                        ================================================== --}}

                                        @case('free_gift')

                                            @php
                                                $freeItem = $deal->freeItems->first()?->menuItem;
                                            @endphp


                                            <div class="thumb">

                                                <a
                                                    href="{{ $freeItem ? route('storefront.dish', $freeItem->slug) : '#' }}"
                                                    class="image"
                                                >

                                                    <img
                                                        src="{{ $deal->image ? Storage::url($deal->image) : asset('storefront/assets/images/products/medium-size/1.jpg') }}"
                                                        alt="{{ $deal->name }}"
                                                    >

                                                </a>


                                                <span class="badges">

                                                    <span class="sale">
                                                        Free Gift
                                                    </span>

                                                </span>


                                                @if ($freeItem)

                                                    <div class="actions">

                                                       <a
    href="javascript:void(0)"
    class="action quickview"
    data-bs-toggle="modal"
    data-bs-target="#exampleModalCenter"
    data-slug="{{ $target?->slug ?? $buyItem?->slug ?? $freeItem?->slug ?? $comboFirstItem?->slug }}"
    data-deal-id="{{ $deal->id }}"
    title="Quick View"
>
    <i class="pe-7s-search"></i>
</a>

                                                    </div>

                                                @endif

                                            </div>


                                            <div class="content">

                                                <span class="type-label">
                                                    {{ $typeLabels[$deal->type] ?? 'Free Gift' }}
                                                </span>


                                                <h5 class="title">
                                                    {{ $deal->name }}
                                                </h5>


                                                <p class="desc-line">

                                                    Free
                                                    {{ $freeItem?->name ?? 'item' }}
                                                    when you spend
                                                    £{{ number_format($deal->min_spend, 2) }}
                                                    or more.

                                                </p>


                                                <div class="shop-list-btn">

                                                    @include('storefront.partials.add-bundle-to-cart-button', ['deal' => $deal, 'menuItem' => $freeItem])

                                                </div>

                                            </div>

                                            @break


                                        {{-- =================================================
                                             COMBO / BUNDLE
                                        ================================================== --}}

                                        @case('combo')
                                        @case('bundle')

                                            @php

                                                $comboFirstItem =
                                                    $deal->bundleComponents
                                                        ->first()?->menuItem;

                                                $comboBadgeLabel =
                                                    $deal->type === 'bundle'
                                                        ? 'Bundle'
                                                        : 'Combo';

                                            @endphp


                                            <div class="thumb">

                                                <img
                                                    src="{{ $deal->image ? Storage::url($deal->image) : asset('storefront/assets/images/products/medium-size/1.jpg') }}"
                                                    alt="{{ $deal->name }}"
                                                >


                                                <span class="badges">

                                                    <span class="sale">
                                                        {{ $comboBadgeLabel }}
                                                    </span>

                                                </span>


                                                @if ($comboFirstItem)

                                                    <div class="actions">

                                                       <a
    href="javascript:void(0)"
    class="action quickview"
    data-bs-toggle="modal"
    data-bs-target="#exampleModalCenter"
    data-slug="{{ $target?->slug ?? $buyItem?->slug ?? $freeItem?->slug ?? $comboFirstItem?->slug }}"
    data-deal-id="{{ $deal->id }}"
    title="Quick View"
>
    <i class="pe-7s-search"></i>
</a>

                                                    </div>

                                                @endif

                                            </div>


                                            <div class="content">

                                                <span class="type-label">
                                                    {{ $typeLabels[$deal->type] ?? ucfirst($deal->type) }}
                                                </span>


                                                <h5 class="title">
                                                    {{ $deal->name }}
                                                </h5>


                                                <p class="desc-line">

                                                    {{
                                                        $deal->bundleComponents
                                                            ->pluck('menuItem.name')
                                                            ->filter()
                                                            ->join(' + ')
                                                    }}

                                                </p>


                                                <span class="price">

                                                    <span class="new">
                                                        £{{ number_format($deal->combo_price, 2) }}
                                                    </span>

                                                </span>


                                                <div class="shop-list-btn">

                                                    @include('storefront.partials.add-bundle-to-cart-button', ['deal' => $deal])

                                                </div>

                                            </div>

                                            @break


                                        {{-- =================================================
                                             PROMO CODE
                                        ================================================== --}}

                                        @case('promo_code')

                                            <div class="thumb icon-tile tone-gold">

                                                <i class="fa fa-tag"></i>

                                            </div>


                                            <div class="content">

                                                <span class="type-label">
                                                    {{ $typeLabels[$deal->type] ?? 'Promo Code' }}
                                                </span>


                                                <h5 class="title">
                                                    {{ $deal->name }}
                                                </h5>


                                                <p class="desc-line mb-1">

                                                    {{
                                                        $deal->discount_type === 'percentage'
                                                            ? $deal->discount_value . '% off'
                                                            : '£' . number_format($deal->discount_value, 2) . ' off'
                                                    }}

                                                </p>


                                                <p class="desc-line mb-0">

                                                    Code:

                                                    <span class="font-monospace fw-bold">
                                                        {{ $deal->promo_code }}
                                                    </span>

                                                </p>


                                                <div class="shop-list-btn">

                                                    <a href="{{ route('storefront.home') }}" class="btn btn-outline-dark btn-hover-primary">
                                                        <i class="fa fa-utensils me-2"></i>
                                                        Browse Menu
                                                    </a>

                                                </div>

                                            </div>

                                            @break


                                        {{-- =================================================
                                             DEFAULT
                                        ================================================== --}}

                                        @default

                                            <div class="thumb icon-tile tone-ember">

                                                <i class="fa fa-star"></i>

                                            </div>


                                            <div class="content">

                                                <span class="type-label">
                                                    {{ $typeLabels[$deal->type] ?? ucfirst(str_replace('_', ' ', $deal->type)) }}
                                                </span>


                                                <h5 class="title">
                                                    {{ $deal->name }}
                                                </h5>


                                                @if ($deal->scheduleSummary())

                                                    <p class="desc-line">
                                                        {{ $deal->scheduleSummary() }}
                                                    </p>

                                                @endif


                                                <div class="shop-list-btn">

                                                    <a href="{{ route('storefront.home') }}" class="btn btn-outline-dark btn-hover-primary">
                                                        <i class="fa fa-utensils me-2"></i>
                                                        Browse Menu
                                                    </a>

                                                </div>

                                            </div>

                                    @endswitch

                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="empty-deals">

                            <i class="fa fa-tags fa-2x mb-3"></i>

                            <p class="mb-0">
                                No deals running right now — check back soon!
                            </p>

                        </div>

                    @endforelse

                </div>


                {{-- =================================================
                     PAGINATION
                ================================================== --}}

                @if ($deals->hasPages())

                    <div
                        class="shop_toolbar_wrapper mt-10"
                        data-aos="fade-up"
                        data-aos-delay="200"
                    >

                        <div class="shop-top-bar-left">

                            <span>
                                Page
                                {{ $deals->currentPage() }}
                                of
                                {{ $deals->lastPage() }}
                            </span>

                        </div>


                        <div class="shop-top-bar-right">

                            {{ $deals->onEachSide(1)->links() }}

                        </div>

                    </div>

                @endif

            </div>


            {{-- =================================================
                 SIDEBAR
                 (unchanged fields/variables — $search, $activeType,
                 $typeLabels, $typeCounts, $recentDeals — just restyled)
            ================================================== --}}

            <div class="col-lg-3 col-12 col-custom deals-filter-col" id="deals-filter-panel">

                <aside class="sidebar_widget mt-10 mt-lg-0">

                    <div
                        class="widget_inner"
                        data-aos="fade-up"
                        data-aos-delay="200"
                    >
                        <div class="d-flex justify-content-between align-items-center d-lg-none mb-3 deals-filter-drawer-head">
                            <h5 class="mb-0">Filter Deals</h5>
                            <button type="button" id="deals-filter-close" aria-label="Close filters">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
<!-- 

                        {{-- SEARCH --}}

                        <div class="widget-list mb-10">

                            <h3 class="widget-title mb-4">
                                Search
                            </h3>


                            <form
                                method="GET"
                                action="{{ route('storefront.deals.index') }}"
                                class="search-box"
                            >

                                <input
                                    type="hidden"
                                    name="type"
                                    value="{{ $activeType }}"
                                > -->

                                <!-- <input
                                    type="text"
                                    name="search"
                                    value="{{ $search }}"
                                    class="form-control"
                                    placeholder="Search deals..."
                                    aria-label="Search deals"
                                >


                                <button
                                    class="btn btn-dark btn-hover-primary"
                                    type="submit"
                                >
                                    <i class="fa fa-search"></i>
                                </button> -->

                            <!-- </form>

                        </div>  -->


                        {{-- DEAL TYPES --}}

                        <div class="widget-list mb-10">

                            <h3 class="widget-title mb-4">
                                Deal Type
                            </h3>


                            <div class="sidebar-body">

                                <ul class="sidebar-list">

                                    <li>

                                        <a
                                            href="{{ route('storefront.deals.index') }}"
                                            class="{{ ! $activeType ? 'active' : '' }}"
                                        >
                                            All Deals
                                            ({{ $typeCounts->sum() }})
                                        </a>

                                    </li>


                                    @foreach ($typeLabels as $key => $label)

                                        <li>

                                            <a
                                                href="{{ route('storefront.deals.index', ['type' => $key]) }}"
                                                class="{{ $activeType === $key ? 'active' : '' }}"
                                            >

                                                {{ $label }}

                                                ({{ $typeCounts[$key] ?? 0 }})

                                            </a>

                                        </li>

                                    @endforeach

                                </ul>

                            </div>

                        </div>


                        {{-- RECENTLY ADDED --}}

                        <div class="widget-list">

                            <h3 class="widget-title mb-4">
                                Recently Added
                            </h3>


                            <div class="sidebar-body product-list-wrapper mb-n6">

                                @forelse ($recentDeals as $recentDeal)

                                    <div class="single-product-list product-hover mb-6">

                                        <div class="content">

                                            <h5 class="title">
                                                {{ $recentDeal->name }}
                                            </h5>


                                            <span class="d-block small text-muted">

                                                {{
                                                    $typeLabels[$recentDeal->type]
                                                    ?? ucfirst(
                                                        str_replace(
                                                            '_',
                                                            ' ',
                                                            $recentDeal->type
                                                        )
                                                    )
                                                }}

                                            </span>

                                        </div>

                                    </div>

                                @empty

                                    <p class="mb-0 small text-muted">
                                        No recent deals.
                                    </p>

                                @endforelse

                            </div>

                        </div>

                    </div>

                </aside>

            </div>

            <div class="deals-filter-backdrop" id="deals-filter-backdrop"></div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {


    /* =========================================================
       COUNTDOWN
    ========================================================== */

    const wrappers =
        document.querySelectorAll('[data-countdown]');


    function buildSkeleton(el) {

        el.innerHTML =

            '<div class="single-countdown">' +
                '<span class="single-countdown_time cd-days">00</span>' +
                '<span class="single-countdown_text">Days</span>' +
            '</div>' +

            '<div class="single-countdown">' +
                '<span class="single-countdown_time cd-hours">00</span>' +
                '<span class="single-countdown_text">Hours</span>' +
            '</div>' +

            '<div class="single-countdown">' +
                '<span class="single-countdown_time cd-mins">00</span>' +
                '<span class="single-countdown_text">Min</span>' +
            '</div>' +

            '<div class="single-countdown">' +
                '<span class="single-countdown_time cd-secs">00</span>' +
                '<span class="single-countdown_text">Sec</span>' +
            '</div>';

    }


    function parseCountdownDate(str) {

        const parts = str.split(' ');

        const datePart = parts[0];
        const timePart = parts[1] || '00:00:00';


        const dateParts =
            datePart.split('/').map(Number);

        const timeParts =
            timePart.split(':').map(Number);


        const y = dateParts[0];
        const m = dateParts[1];
        const d = dateParts[2];


        const hh = timeParts[0] || 0;
        const mm = timeParts[1] || 0;
        const ss = timeParts[2] || 0;


        return new Date(
            y,
            m - 1,
            d,
            hh,
            mm,
            ss
        );

    }


    if (wrappers.length) {

        wrappers.forEach(buildSkeleton);


        function tick() {

            wrappers.forEach(function (el) {

                const targetDate =
                    parseCountdownDate(
                        el.dataset.countdown
                    );


                const diff =
                    Math.max(
                        0,
                        targetDate.getTime() - Date.now()
                    );


                const days =
                    Math.floor(
                        diff / 86400000
                    );


                const hours =
                    Math.floor(
                        (diff % 86400000) /
                        3600000
                    );


                const mins =
                    Math.floor(
                        (diff % 3600000) /
                        60000
                    );


                const secs =
                    Math.floor(
                        (diff % 60000) /
                        1000
                    );


                const pad =
                    n => String(n).padStart(2, '0');


                const daysElement =
                    el.querySelector('.cd-days');

                const hoursElement =
                    el.querySelector('.cd-hours');

                const minsElement =
                    el.querySelector('.cd-mins');

                const secsElement =
                    el.querySelector('.cd-secs');


                if (daysElement) {
                    daysElement.textContent =
                        pad(days);
                }

                if (hoursElement) {
                    hoursElement.textContent =
                        pad(hours);
                }

                if (minsElement) {
                    minsElement.textContent =
                        pad(mins);
                }

                if (secsElement) {
                    secsElement.textContent =
                        pad(secs);
                }

                // Pulse the whole countdown once under an hour remains,
                // to make genuinely-urgent deals visually stand out.
                el.querySelectorAll('.single-countdown').forEach(function (box) {
                    box.classList.toggle('cd-urgent', diff > 0 && diff < 3600000);
                });

            });

        }


        tick();

        setInterval(
            tick,
            1000
        );

    }


    /* =========================================================
       SEARCH TOGGLE (top-right corner icon)
    ========================================================== */

    const searchToggle = document.getElementById('deals-search-toggle');
    const searchForm = document.getElementById('deals-search-form');

    if (searchToggle && searchForm) {

        searchToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            searchForm.classList.toggle('open');
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.deals-search-wrap')) {
                searchForm.classList.remove('open');
            }
        });
    }


    /* =========================================================
       MOBILE FILTER DRAWER
    ========================================================== */

    const filterPanel = document.getElementById('deals-filter-panel');
    const filterBackdrop = document.getElementById('deals-filter-backdrop');
    const filterOpenBtn = document.getElementById('deals-filter-open');
    const filterCloseBtn = document.getElementById('deals-filter-close');

    function openFilterDrawer() {
        if (!filterPanel) return;
        filterPanel.classList.add('open');
        filterBackdrop?.classList.add('open');
        document.body.classList.add('fix');
    }

    function closeFilterDrawer() {
        if (!filterPanel) return;
        filterPanel.classList.remove('open');
        filterBackdrop?.classList.remove('open');
        document.body.classList.remove('fix');
    }

    filterOpenBtn?.addEventListener('click', openFilterDrawer);
    filterCloseBtn?.addEventListener('click', closeFilterDrawer);
    filterBackdrop?.addEventListener('click', closeFilterDrawer);


    /* =========================================================
       GRID / LIST TOGGLE
    ========================================================== */

    const dealsWrapper =
        document.getElementById('deals-wrapper');


    const toggleButtons =
        document.querySelectorAll(
            '.shop_toolbar_btn button[data-role]'
        );


    if (
        dealsWrapper &&
        toggleButtons.length
    ) {


        toggleButtons.forEach(function (btn) {

            btn.addEventListener(
                'click',
                function () {

                    const role =
                        btn.dataset.role;


                    dealsWrapper.classList.remove(
                        'grid_3',
                        'grid_list'
                    );


                    dealsWrapper.classList.add(
                        role
                    );


                    toggleButtons.forEach(
                        function (button) {

                            button.classList.remove(
                                'active'
                            );

                        }
                    );


                    btn.classList.add(
                        'active'
                    );


                    try {

                        localStorage.setItem(
                            'deals_layout',
                            role
                        );

                    } catch (e) {

                        // Ignore localStorage errors.

                    }

                }
            );

        });


        /* =====================================================
           RESTORE SAVED LAYOUT
        ====================================================== */

        try {

            const savedLayout =
                localStorage.getItem(
                    'deals_layout'
                );


            if (
                savedLayout === 'grid_3' ||
                savedLayout === 'grid_list'
            ) {

                dealsWrapper.classList.remove(
                    'grid_3',
                    'grid_list'
                );


                dealsWrapper.classList.add(
                    savedLayout
                );


                toggleButtons.forEach(
                    function (button) {

                        button.classList.toggle(
                            'active',
                            button.dataset.role === savedLayout
                        );

                    }
                );

            }

        } catch (e) {

            // Default list view remains active.

        }

    }


    /* =========================================================
       QUICK VIEW
    ========================================================== */

    document.querySelectorAll(
        '.deals-list-page .quickview'
    ).forEach(function (button) {

        button.addEventListener(
            'click',
            function () {

                const slug =
                    button.dataset.slug;

                if (!slug) {
                    return;
                }

                /*
                 * The existing theme/modal JavaScript can use
                 * data-slug to load the product.
                 *
                 * This handler intentionally does not make
                 * an AJAX request because the actual Quick View
                 * endpoint belongs to the storefront theme.
                 */

            }
        );

    });

});

</script>

@endpush