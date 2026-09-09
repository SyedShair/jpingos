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
        top: 10px;
        left: 10px;
        z-index: 3;
    }

    .deals-list-page .deal-row .badges .sale {
        display: inline-block;
        background: var(--ember);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        line-height: 1;
        letter-spacing: .02em;
        padding: 7px 11px;
        border-radius: 20px;
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
        font-size: 18px;
        font-weight: 700;
        line-height: 1.3;
        margin-bottom: 6px;
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


    /* =========================================================
       PRICE
    ========================================================== */

    .deals-list-page .deal-row .content .price {
        display: block;
        margin-top: 6px;
    }

    .deals-list-page .deal-row .content .price .new {
        color: var(--ember-dark);
        font-family: 'Fraunces', serif;
        font-size: 19px;
        font-weight: 700;
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
        font-weight: 600;
        letter-spacing: .01em;

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
        border-left: 4px solid var(--ember);
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
        border-top: 4px solid var(--ember);
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
        font-size: 16px;
    }

    .deals-list-page .deals_wrapper.grid_3 .deal-row .content .desc-line {
        display: none;
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
       TOOLBAR
    ========================================================== */

    .deals-list-page .shop_toolbar_btn button.active {
        color: var(--ember-dark);
    }

    .deals-list-page .shop_toolbar_btn button:hover {
        color: var(--ember-dark);
    }


    /* =========================================================
       SIDEBAR
    ========================================================== */

    .deals-list-page .sidebar-list a {
        transition: color .2s ease;
    }

    .deals-list-page .sidebar-list a:hover {
        color: var(--ember-dark);
    }

    .deals-list-page .sidebar-list a.active {
        color: var(--ember-dark);
        font-weight: 600;
    }


    /* =========================================================
       SEARCH
    ========================================================== */

    .deals-list-page .search-box {
        position: relative;
        display: flex;
    }

    .deals-list-page .search-box input {
        padding-right: 50px;
    }

    .deals-list-page .search-box button {
        min-width: 45px;
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

    }


    @media (max-width: 575px) {

        .deals-list-page .deals_wrapper.grid_3 .product {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .deals-list-page .deals_wrapper.grid_list .deal-row .product-inner {
            flex-direction: column;
            border-left: 0;
            border-top: 4px solid var(--ember);
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

</style>

@endpush


@section('content')

@php
    $orderPhone = config('restaurant.order_phone', '+10123456789');
@endphp


{{-- =========================================================
     BREADCRUMB
========================================================= --}}

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

</div>


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
                ================================================== --}}

                <div
                    class="shop_toolbar_wrapper flex-column flex-md-row mb-10"
                    data-aos="fade-up"
                    data-aos-delay="100"
                >

                    <div class="shop-top-bar-left mb-md-0 mb-2">

                        <div class="shop-top-show">

                            <span>

                                @if ($deals->total() > 0)

                                    Showing
                                    {{ $deals->firstItem() }}–{{ $deals->lastItem() }}
                                    of
                                    {{ $deals->total() }}
                                    deals

                                @else

                                    No deals found

                                @endif

                            </span>

                        </div>

                    </div>


                    <div class="shop-top-bar-right">

                        <form
                            method="GET"
                            action="{{ route('storefront.deals.index') }}"
                            id="deals-toolbar-form"
                        >

                            <input
                                type="hidden"
                                name="search"
                                value="{{ $search }}"
                            >

                            <input
                                type="hidden"
                                name="type"
                                value="{{ $activeType }}"
                            >


                            {{-- PER PAGE --}}

                            <div class="shop-short-by mr-4 d-inline-block">

                                <select
                                    class="nice-select"
                                    name="per_page"
                                    onchange="this.form.submit()"
                                    aria-label="Deals per page"
                                >

                                    @foreach ([12, 24, 30] as $option)

                                        <option
                                            value="{{ $option }}"
                                            @selected($perPage === $option)
                                        >
                                            Show {{ $option }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            {{-- SORT --}}

                            <div class="shop-short-by mr-4 d-inline-block">

                                <select
                                    class="nice-select"
                                    name="sort"
                                    onchange="this.form.submit()"
                                    aria-label="Sort deals"
                                >

                                    <option
                                        value="default"
                                        @selected($sort === 'default')
                                    >
                                        Sort by Name
                                    </option>

                                    <option
                                        value="latest"
                                        @selected($sort === 'latest')
                                    >
                                        Sort by Latest
                                    </option>

                                </select>

                            </div>

                        </form>


                        {{-- GRID / LIST --}}

                        <div class="shop_toolbar_btn">

                            <button
                                type="button"
                                class="btn-grid-4"
                                data-role="grid_3"
                                title="Grid"
                            >
                                <i class="fa fa-th"></i>
                            </button>

                            <button
                                type="button"
                                class="active btn-list"
                                data-role="grid_list"
                                title="List"
                            >
                                <i class="fa fa-th-list"></i>
                            </button>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     DEALS
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
            ================================================== --}}

            <div class="col-lg-3 col-12 col-custom">

                <aside class="sidebar_widget mt-10 mt-lg-0">

                    <div
                        class="widget_inner"
                        data-aos="fade-up"
                        data-aos-delay="200"
                    >


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
                                >

                                <input
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
                                </button>

                            </form>

                        </div>


                        {{-- DEAL TYPES --}}

                        <div class="widget-list mb-10">

                            <h3 class="widget-title">
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

            });

        }


        tick();

        setInterval(
            tick,
            1000
        );

    }


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