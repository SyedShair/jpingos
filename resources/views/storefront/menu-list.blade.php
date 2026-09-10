@extends('storefront.layouts.app')

@section('title', $pageTitle . ' | ' . config('app.name', 'Restaurant'))

@push('styles')
<style>
    /* =========================================================
       HOVER IMAGE SWAP
       Overrides the sitewide "IMAGE FIX" rule (which forces
       first-image to stay opaque even on hover) so both the main
       dish grid AND the Recent Dishes sidebar list work correctly
       for items with a second image, while items with only one
       image keep the always-visible fallback.
    ========================================================= */
    .shop_wrapper .product .thumb .image,
    .sidebar_widget .single-product-list .thumb .image {
        position: relative;
        overflow: hidden;
    }

    .shop_wrapper .product .thumb .image img.first-image,
    .shop_wrapper .product .thumb .image img.second-image,
    .sidebar_widget .single-product-list .thumb .image img.first-image,
    .sidebar_widget .single-product-list .thumb .image img.second-image {
        transition: opacity .3s ease, transform .4s ease;
    }

    .shop_wrapper .product .thumb .image img.second-image,
    .sidebar_widget .single-product-list .thumb .image img.second-image {
        position: absolute;
        top: 0;
        left: 0;
        opacity: 0 !important;
    }

    .shop_wrapper .product .thumb:hover .image img.first-image,
    .sidebar_widget .single-product-list .thumb:hover .image img.first-image {
        opacity: 0 !important;
    }

    .shop_wrapper .product .thumb:hover .image img.second-image,
    .sidebar_widget .single-product-list .thumb:hover .image img.second-image {
        opacity: 1 !important;
    }

    .shop_wrapper .product .thumb .image:not(:has(img.second-image)) img.first-image,
    .shop_wrapper .product .thumb:hover .image:not(:has(img.second-image)) img.first-image,
    .sidebar_widget .single-product-list .thumb .image:not(:has(img.second-image)) img.first-image,
    .sidebar_widget .single-product-list .thumb:hover .image:not(:has(img.second-image)) img.first-image {
        opacity: 1 !important;
    }

    /* Subtle zoom on hover, on top of whichever image is currently
       showing (first or second) — works whether or not a swap
       is happening at all. */
    .shop_wrapper .product .thumb:hover .image img,
    .sidebar_widget .single-product-list .thumb:hover .image img {
        transform: scale(1.06);
    }

    /* =========================================================
       TOOLBAR
    ========================================================= */
    .menu-toolbar-panel {
        background: #f7f7f8;
        border: 1px solid #ececec;
        border-radius: 12px;
        padding: 20px;
    }

    .menu-toolbar-count {
        color: #6b7280;
        font-size: 14px;
        margin-bottom: 12px;
    }

    .menu-toolbar-form {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: stretch;
    }

    .menu-toolbar-form .toolbar-search,
    .menu-toolbar-form select.toolbar-select {
        background: #fff;
        border: 1px solid #e3e3e6;
        border-radius: 8px;
        height: 48px;
        padding: 0 16px;
        font-size: 14px;
        color: #333;
    }

    .menu-toolbar-form .toolbar-search {
        flex: 2 1 220px;
    }

    .menu-toolbar-form select.toolbar-select {
        flex: 1 1 150px;
    }

    .menu-toolbar-form .toolbar-submit {
        flex: 0 0 auto;
        background: #111;
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

    .menu-toolbar-form .toolbar-submit:hover {
        background: #000;
    }

    .menu-toolbar-viewtoggle {
        display: flex;
        gap: 8px;
        margin-left: auto;
    }

    .menu-toolbar-viewtoggle button {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 1px solid #e3e3e6;
        background: #fff;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .2s ease;
    }

    .menu-toolbar-viewtoggle button.active,
    .menu-toolbar-viewtoggle button:hover {
        background: #111;
        border-color: #111;
        color: #fff;
    }

    /* =========================================================
       PRODUCT CARDS
    ========================================================= */
    .shop_wrapper .product {
        margin-bottom: 24px;
    }

    .shop_wrapper .product .product-inner {
        background: #fff;
        border: 1px solid #ececec;
        border-radius: 12px;
        overflow: hidden;
        height: 100%;
        transition: box-shadow .2s ease, transform .2s ease;
    }

    .shop_wrapper .product:hover .product-inner {
        box-shadow: 0 10px 24px rgba(0, 0, 0, .07);
        transform: translateY(-2px);
    }

    .shop_wrapper .product .thumb {
        position: relative;
        background: #f2f2f2;
    }

    .shop_wrapper .product .thumb .image {
        display: block;
        aspect-ratio: 1 / 1;
    }

    .shop_wrapper .product .thumb .image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .shop_wrapper .product .badges {
        position: absolute;
        top: 12px;
        left: 12px;
        z-index: 2;
    }

    .shop_wrapper .product .badges .sale-badge {
        display: inline-block;
        background: #1a9c5c;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .shop_wrapper .product .badges .featured-badge {
        display: inline-block;
        background: #d9534f;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .shop_wrapper .product .content {
        padding: 18px;
    }

    .shop_wrapper .product .content .sub-title {
        display: none;
    }

    .shop_wrapper .product .content .title {
        font-size: 15px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 8px;
        line-height: 1.4;
    }

    .shop_wrapper .product .content .title a {
        color: #1a1a1a;
    }

    .shop_wrapper .product .content .ratings {
        display: block;
        margin-bottom: 8px;
        color: #f5a623;
        font-size: 13px;
    }

    .shop_wrapper .product .content .ratings .rating-num {
        color: #9ca3af;
        margin-left: 4px;
    }

    .shop_wrapper .product .content .price {
        display: block;
        margin-bottom: 14px;
        font-size: 16px;
    }

    .shop_wrapper .product .content .price .new {
        color: #1a9c5c;
        font-weight: 700;
        margin-right: 8px;
    }

    .shop_wrapper .product .content .price .old {
        color: #9ca3af;
        text-decoration: line-through;
        font-weight: 400;
    }

    .shop_wrapper .product .content .shop-list-btn {
        display: flex;
        gap: 8px;
    }

    .shop_wrapper .product .content .shop-list-btn a {
        flex: 1;
        border-radius: 8px;
        text-transform: uppercase;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .02em;
    }

    /* Grid mode: centered card, no description */
    .shop_wrapper:not(.grid_list) .product .content {
        text-align: center;
    }

    .shop_wrapper:not(.grid_list) .product .content p {
        display: none;
    }

    .shop_wrapper:not(.grid_list) .product .content .ratings,
    .shop_wrapper:not(.grid_list) .product .content .price {
        justify-content: center;
    }

    .shop_wrapper:not(.grid_list) .product .content .shop-list-btn a:first-child {
        flex: 0 0 44px;
    }

    /* List mode: image left, content right */
    .shop_wrapper.grid_list .product .product-inner {
        display: flex;
        align-items: stretch;
    }

    .shop_wrapper.grid_list .product .thumb {
        flex: 0 0 220px;
    }

    .shop_wrapper.grid_list .product .thumb .image {
        aspect-ratio: auto;
        height: 100%;
    }

    .shop_wrapper.grid_list .product .content {
        flex: 1;
        text-align: left;
    }

    /* =========================================================
       SIDEBAR CATEGORIES
    ========================================================= */
    .sidebar_widget .widget_inner {
        background: #fff;
        border: 1px solid #ececec;
        border-radius: 12px;
        padding: 24px;
    }

    .sidebar_widget .widget-title {
        font-weight: 800;
        font-size: 18px;
        color: #1a1a1a;
    }

    .sidebar_widget .category-menu > li {
        border-bottom: none;
    }

    .sidebar_widget .category-menu > li > a {
        display: block;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 13px;
        letter-spacing: .02em;
        color: #1a1a1a;
        padding-bottom: 8px;
        border-bottom: 1px solid #ececec;
        margin-bottom: 8px;
    }

    .sidebar_widget .category-menu .dropdown li a {
        display: block;
        padding: 6px 0;
        color: #4b5563;
        font-size: 14px;
    }

    .sidebar_widget .category-menu .dropdown li a.fw-bold,
    .sidebar_widget .category-menu > li > a.fw-bold {
        color: #1a9c5c;
    }

    .sidebar_widget .category-menu .menu-item-has-children {
        position: relative;
    }

    .sidebar_widget .category-menu .menu-expand {
        position: absolute;
        top: 2px;
        right: 0;
        width: 22px;
        height: 22px;
        border: 1px solid #d1d5db;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        color: #6b7280;
        cursor: pointer;
    }

    /* Recent Dishes hover animation */
    .sidebar_widget .single-product-list {
        border-radius: 10px;
        transition: box-shadow .2s ease, transform .2s ease;
    }

    .sidebar_widget .single-product-list:hover {
        box-shadow: 0 6px 16px rgba(0, 0, 0, .06);
        transform: translateY(-2px);
    }

    /* =========================================================
       MOBILE FILTER / CATEGORIES DRAWER
       Framework-independent: does not rely on Bootstrap's
       Offcanvas JS component (which silently no-ops if the
       theme's Bootstrap build/version doesn't include it).
       Controlled instead by menuFiltersOpenBtn/CloseBtn below.
    ========================================================= */
    .mobile-menu-toolbar {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .mobile-pill-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        background: #fff;
        border: 1px solid #ff4545;
        color: #ff4545;
        border-radius: 30px;
        padding: 12px 20px;
        font-weight: 700;
        font-size: 15px;
    }

    .mobile-pill-row {
        display: flex;
        gap: 10px;
    }

    .mobile-pill-select-wrap {
        flex: 1;
        position: relative;
    }

    .mobile-pill-select-wrap i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        pointer-events: none;
    }

    .mobile-pill-select-wrap select {
        width: 100%;
        appearance: none;
        background: #fff;
        border: 1px solid #e3e3e6;
        border-radius: 30px;
        padding: 10px 16px 10px 38px;
        font-weight: 700;
        font-size: 14px;
        color: #1a1a1a;
    }

    /* Desktop: sidebar renders inline, drawer chrome not needed */
    @media (min-width: 992px) {
        .sidebar_widget .offcanvas-header {
            display: none;
        }
    }

    /* Mobile: custom sliding drawer + backdrop, independent of
       Bootstrap's offcanvas component/version. */
    @media (max-width: 991.98px) {
        .sidebar_widget.offcanvas-lg {
            position: fixed;
            top: 0;
            left: 0;
            width: 300px;
            max-width: 85vw;
            height: 100vh;
            overflow-y: auto;
            background: #fff;
            z-index: 1045;
            transform: translateX(-100%);
            transition: transform .3s ease;
            box-shadow: 2px 0 16px rgba(0, 0, 0, .15);
        }

        .sidebar_widget.offcanvas-lg.show {
            transform: translateX(0);
        }

        .menu-filters-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            z-index: 1040;
            opacity: 0;
            pointer-events: none;
            transition: opacity .3s ease;
        }

        .menu-filters-backdrop.show {
            opacity: 1;
            pointer-events: auto;
        }

        body.menu-filters-open {
            overflow: hidden;
        }

        .sidebar_widget .category-menu > li > a {
            font-size: 15px;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }

        .sidebar_widget .category-menu .dropdown li a {
            font-size: 16px;
            padding: 10px 0;
        }

        .sidebar_widget .category-menu .menu-item-has-children {
            padding-top: 4px;
        }
    }
</style>
@endpush

@section('content')

 

  <div class="section section-margin">
    <div class="container">
      <div class="row flex-row-reverse">

        <!-- Dish list -->
        <div class="col-lg-9 col-12 col-custom">

          <!-- Toolbar Start (desktop) -->
          <div class="menu-toolbar-panel mb-10 d-none d-lg-block">
            <div class="menu-toolbar-count">
              Showing {{ $dishes->firstItem() ?? 0 }}–{{ $dishes->lastItem() ?? 0 }} of {{ $dishes->total() }} dishes
            </div>

            {{-- Search + sort + per-page in one row, one submit.
                 NOTE: the controller needs to read request('per_page')
                 when building $dishes->paginate(...) for the per-page
                 select to actually change the page size. --}}
            <form method="GET" id="filters-form" class="menu-toolbar-form">
              <input type="text" name="search" value="{{ request('search') }}"
                class="toolbar-search" placeholder="Search products" aria-label="Search the menu">

              <select class="toolbar-select" name="sort" aria-label="Sort dishes">
                <option value="name" @selected(request('sort', 'name') === 'name')>Sort by Name</option>
                <option value="price_low" @selected(request('sort') === 'price_low')>Price: Low to High</option>
                <option value="price_high" @selected(request('sort') === 'price_high')>Price: High to Low</option>
                <option value="latest" @selected(request('sort') === 'latest')>Sort: Latest</option>
              </select>

              <select class="toolbar-select" name="per_page" aria-label="Dishes per page">
                <option value="12" @selected(request('per_page', 12) == 12)>12 / page</option>
                <option value="24" @selected(request('per_page') == 24)>24 / page</option>
                <option value="36" @selected(request('per_page') == 36)>36 / page</option>
              </select>

              <button type="submit" class="toolbar-submit">Search</button>

              {{-- Grid / List toggle. custom.js already listens for clicks
                   on .shop_toolbar_btn > button and toggles the classes
                   below on .shop_wrapper — no JS changes needed, only the
                   wrapper class name here (shop_toolbar_btn) matters. --}}
              <div class="shop_toolbar_btn menu-toolbar-viewtoggle">
                <button data-role="grid_3" type="button" class="active" title="Grid"><i class="fa fa-th"></i></button>
                <button data-role="grid_list" type="button" title="List"><i class="fa fa-th-list"></i></button>
              </div>
            </form>
          </div>
          <!-- Toolbar End (desktop) -->

          <!-- Toolbar Start (mobile) -->
          <div class="mobile-menu-toolbar mb-10 d-lg-none">
            <button type="button" class="mobile-pill-btn" id="menuFiltersOpenBtn" aria-controls="menuFiltersOffcanvas">
              <i class="fa fa-sliders"></i> Filter &amp; Categories
            </button>

            <div class="mobile-pill-row">
              {{-- Own small auto-submitting form, independent of the
                   desktop #filters-form, so hidden fields don't collide
                   with it when both exist in the DOM at once. --}}
              <form method="GET" id="mobile-sort-form" class="mobile-pill-select-wrap">
                @if (request('search'))
                  <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if (request('per_page'))
                  <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <i class="fa fa-sort"></i>
                <select name="sort" aria-label="Sort dishes" onchange="document.getElementById('mobile-sort-form').submit()">
                  <option value="name" @selected(request('sort', 'name') === 'name')>Sort</option>
                  <option value="price_low" @selected(request('sort') === 'price_low')>Price: Low to High</option>
                  <option value="price_high" @selected(request('sort') === 'price_high')>Price: High to Low</option>
                  <option value="latest" @selected(request('sort') === 'latest')>Latest</option>
                </select>
              </form>

              <form method="GET" id="mobile-per-page-form" class="mobile-pill-select-wrap">
                @if (request('search'))
                  <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if (request('sort'))
                  <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                <i class="fa fa-list-ol"></i>
                <select name="per_page" aria-label="Dishes per page" onchange="document.getElementById('mobile-per-page-form').submit()">
                  <option value="12" @selected(request('per_page', 12) == 12)>Per Page</option>
                  <option value="24" @selected(request('per_page') == 24)>24 / page</option>
                  <option value="36" @selected(request('per_page') == 36)>36 / page</option>
                </select>
              </form>
            </div>
          </div>
          <!-- Toolbar End (mobile) -->

          <!-- Shop Wrapper Start -->
          <div class="row shop_wrapper grid_3">
            @forelse ($dishes as $dish)
              @php
                $savings = $dish->is_on_sale ? max($dish->price - $dish->discount_price, 0) : 0;
              @endphp
              <div class="col-lg-4 col-md-4 col-sm-6 product">
                <div class="product-inner">
                  <div class="thumb">
                    <a href="{{ route('storefront.dish', $dish->slug) }}" class="image">
                      <img class="first-image" src="{{ $dish->image_url }}" alt="{{ $dish->name }}" />
                      @if (!empty($dish->second_image_url))
                        <img class="second-image" src="{{ $dish->second_image_url }}" alt="{{ $dish->name }}" />
                      @endif
                    </a>
                    @if ($dish->is_on_sale)
                      <span class="badges"><span class="sale-badge">Save £{{ number_format($savings, 2) }}</span></span>
                    @elseif ($dish->is_featured)
                      <span class="badges"><span class="featured-badge">Featured</span></span>
                    @endif
                    <div class="actions">
                      <a href="javascript:void(0)" title="Quickview" class="action quickview" data-bs-toggle="modal" data-bs-target="#exampleModalCenter" data-slug="{{ $dish->slug }}"><i class="pe-7s-search"></i></a>
                    </div>
                  </div>
                  <div class="content">
                    <h4 class="sub-title"><a href="{{ $dish->category ? route('storefront.category', $dish->category->slug) : '#' }}">{{ $dish->category->name ?? 'Menu' }}</a></h4>
                    <h5 class="title"><a href="{{ route('storefront.dish', $dish->slug) }}">{{ $dish->name }}</a></h5>

                    <span class="ratings">
                      <span class="rating-wrap">
                        <span class="star" style="width: 100%"></span>
                      </span>
                      <span class="rating-num">(5)</span>
                    </span>

                    @if ($dish->description)
                      <p>{{ Str::limit($dish->description, 140) }}</p>
                    @endif

                    <span class="price">
                      @if ($dish->is_on_sale)
                        <span class="new">£{{ number_format($dish->discount_price, 2) }}</span>
                        <span class="old">£{{ number_format($dish->price, 2) }}</span>
                      @else
                        <span class="new">£{{ number_format($dish->price, 2) }}</span>
                      @endif
                    </span>

                    <div class="shop-list-btn">
@include('storefront.partials.add-to-cart-button', ['item' => $dish])
                    </div>
                  </div>
                </div>
              </div>
            @empty
              <div class="col-12 text-center text-muted py-5">
                No dishes {{ $activeCategory ? 'in this category ' : '' }}yet.
              </div>
            @endforelse
          </div>
          <!-- Shop Wrapper End -->

          @if ($dishes->hasPages())
            <!-- Toolbar Start -->
            <div class="shop_toolbar_wrapper mt-10">
              <div class="shop-top-bar-right ms-auto">
                {{ $dishes->onEachSide(1)->links() }}
              </div>
            </div>
            <!-- Toolbar End -->
          @endif

        </div>

        <!-- Sidebar -->
        <div class="col-lg-3 col-12 col-custom">
          <aside class="sidebar_widget offcanvas-lg offcanvas-start mt-10 mt-lg-0"
                 tabindex="-1" id="menuFiltersOffcanvas" aria-labelledby="menuFiltersOffcanvasLabel">
            <div class="offcanvas-header d-lg-none">
              <h5 class="offcanvas-title" id="menuFiltersOffcanvasLabel">Filter &amp; Categories</h5>
              <button type="button" class="btn-close" id="menuFiltersCloseBtn" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
            <div class="widget_inner">

              {{-- Mobile-only search — desktop already has search in
                   its own inline toolbar, so this is hidden there via
                   d-lg-none. Own form + hidden fields so it doesn't
                   collide with the desktop #filters-form fields. --}}
              <div class="widget-list mb-10 d-lg-none">
                <h3 class="widget-title mb-4">Search</h3>
                <form action="{{ route('storefront.menu.index') }}" method="GET" class="search-box">
                  @if (request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                  @endif
                  @if (request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                  @endif
                  <input type="text" name="search" value="{{ request('search') }}"
                    class="form-control" placeholder="Search products" aria-label="Search the menu">
                  <button class="btn btn-dark btn-hover-primary" type="submit">Go</button>
                </form>
              </div>

              <div class="widget-list mb-10">
                <h3 class="widget-title mb-4">Categories</h3>
                <nav>
                  <ul class="category-menu mb-n3">
                    <li class="pb-4">
                      <a href="{{ route('storefront.menu.index') }}" class="{{ ! $activeCategory ? 'fw-bold' : '' }}">
                        All Dishes
                      </a>
                    </li>
                    @foreach ($categories as $main)
                      <li class="menu-item-has-children pb-4">
                        <a href="{{ route('storefront.category', $main->slug) }}"
                          class="{{ $activeCategory && $activeCategory->id === $main->id ? 'fw-bold' : '' }}">
                          {{ $main->name }}
                        </a>
                        @if ($main->children->isNotEmpty())
                          <i class="fa fa-angle-down menu-expand"></i>
                        @endif
                        @if ($main->children->isNotEmpty())
                          <ul class="dropdown">
                            @foreach ($main->children as $child)
                              <li>
                                <a href="{{ route('storefront.category', $child->slug) }}"
                                  class="{{ $activeCategory && $activeCategory->id === $child->id ? 'fw-bold' : '' }}">
                                  {{ $child->name }}
                                </a>
                              </li>
                            @endforeach
                          </ul>
                        @endif
                      </li>
                    @endforeach
                  </ul>
                </nav>
              </div>

              {{-- Price filter. Reuses the #slider-range jQuery UI widget
                   already initialized in custom.js. NOTE: the controller
                   needs to read request('price_min')/request('price_max')
                   for the Filter button to actually narrow $dishes. --}}
              <div class="widget-list mb-10">
                <h3 class="widget-title mb-5">Price Filter</h3>
                <form action="{{ route('storefront.menu.index') }}" method="GET">
                  @if ($activeCategory)
                    <input type="hidden" name="category" value="{{ $activeCategory->slug }}">
                  @endif
                  <div id="slider-range"
                       data-min="{{ request('price_min', 0) }}"
                       data-max="{{ request('price_max', 50) }}"></div>
                  <button class="slider-range-submit" type="submit">Filter</button>
                  <input class="slider-range-amount" type="text" name="amount_display" id="amount" readonly />
                  <input type="hidden" name="price_min" id="price_min" value="{{ request('price_min', 0) }}">
                  <input type="hidden" name="price_max" id="price_max" value="{{ request('price_max', 50) }}">
                </form>
              </div>

              @if ($recentDishes->isNotEmpty())
                <div class="widget-list">
                  <h3 class="widget-title mb-4">Recent Dishes</h3>
                  <div class="sidebar-body product-list-wrapper mb-n6">
                    @foreach ($recentDishes as $recent)
                      <div class="single-product-list product-hover mb-6">
                        <div class="thumb">
                          <a href="{{ route('storefront.dish', $recent->slug) }}" class="image">
                            <img class="first-image" src="{{ $recent->image_url }}" alt="{{ $recent->name }}" />
                            @if (!empty($recent->second_image_url))
                              <img class="second-image" src="{{ $recent->second_image_url }}" alt="{{ $recent->name }}" />
                            @endif
                          </a>
                        </div>
                        <div class="content">
                          <h5 class="title"><a href="{{ route('storefront.dish', $recent->slug) }}">{{ $recent->name }}</a></h5>
                          <span class="price">
                            @if ($recent->is_on_sale)
                              <span class="new">£{{ number_format($recent->discount_price, 2) }}</span>
                              <span class="old">£{{ number_format($recent->price, 2) }}</span>
                            @else
                              <span class="new">£{{ number_format($recent->price, 2) }}</span>
                            @endif
                          </span>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endif

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
    // Wire the price slider's live values into the hidden inputs the
    // filter form actually submits. custom.js already creates the
    // #slider-range jQuery UI widget itself; this just keeps the
    // hidden price_min/price_max fields (and the visible readout) in
    // sync with whatever it reports.
    document.addEventListener('DOMContentLoaded', function () {
      var $slider = $('#slider-range');
      if (!$slider.length) return;

      var min = parseFloat($slider.data('min')) || 0;
      var max = parseFloat($slider.data('max')) || 50;

      $slider.on('slide', function (event, ui) {
        document.getElementById('amount').value = '£' + ui.values[0] + ' - £' + ui.values[1];
        document.getElementById('price_min').value = ui.values[0];
        document.getElementById('price_max').value = ui.values[1];
      });

      document.getElementById('amount').value = '£' + min + ' - £' + max;
    });

    // Mobile "Filter & Categories" drawer — framework-independent,
    // does not rely on Bootstrap's Offcanvas JS component (which
    // silently no-ops if the theme's Bootstrap build/version doesn't
    // ship it, which is why the button previously did nothing).
    document.addEventListener('DOMContentLoaded', function () {
      var drawer   = document.getElementById('menuFiltersOffcanvas');
      var openBtn  = document.getElementById('menuFiltersOpenBtn');
      var closeBtn = document.getElementById('menuFiltersCloseBtn');
      if (!drawer || !openBtn) return;

      var backdrop = document.createElement('div');
      backdrop.className = 'menu-filters-backdrop';
      document.body.appendChild(backdrop);

      function openDrawer() {
        drawer.classList.add('show');
        backdrop.classList.add('show');
        document.body.classList.add('menu-filters-open');
      }

      function closeDrawer() {
        drawer.classList.remove('show');
        backdrop.classList.remove('show');
        document.body.classList.remove('menu-filters-open');
      }

      openBtn.addEventListener('click', openDrawer);
      if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
      backdrop.addEventListener('click', closeDrawer);
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeDrawer();
      });
    });
  </script>
@endpush