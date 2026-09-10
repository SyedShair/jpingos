@extends('storefront.layouts.app')

@section('title', config('app.name', 'Restaurant') . ' — Menu & Online Ordering')
@push('styles')

<style>


/* =========================================================
   PRODUCT LIST - IMAGE FIX
========================================================= */

.product-list-wrapper .single-product-list {
    display: flex;
    align-items: flex-start;
    width: 100%;
}

.product-list-wrapper .single-product-list .thumb {
    flex: 0 0 125px;
    width: 125px;
    height: 125px;
    overflow: hidden;
    position: relative;
}

.product-list-wrapper .single-product-list .thumb .image {
    display: block;
    width: 125px;
    height: 125px;
    overflow: hidden;
    position: relative;
}

/* FIRST IMAGE */
.product-list-wrapper .single-product-list .thumb img.first-image {
    width: 125px !important;
    height: 125px !important;
    max-width: 125px !important;
    max-height: 125px !important;
    object-fit: cover !important;
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
    position: relative;
    z-index: 1;
}

/* SECOND IMAGE - only use when it actually exists */
.product-list-wrapper .single-product-list .thumb img.second-image {
    width: 125px !important;
    height: 125px !important;
    max-width: 125px !important;
    max-height: 125px !important;
    object-fit: cover !important;
}

/*
|--------------------------------------------------------------------------
| IMPORTANT
|--------------------------------------------------------------------------
| If there is no second image, NEVER allow the theme hover CSS
| to hide the first image.
*/

.product-list-wrapper .single-product-list:hover
.thumb img.first-image {
    opacity: 1 !important;
    visibility: visible !important;
}

/* If there is no second image, keep first image visible */
.product-list-wrapper .single-product-list .thumb:not(:has(img.second-image))
img.first-image {
    opacity: 1 !important;
    visibility: visible !important;
}

/* Content */
.product-list-wrapper .single-product-list .content {
    flex: 1;
    min-width: 0;
    padding-left: 25px;
}

.product-list-wrapper .single-product-list .content .title {
    margin-top: 0;
    margin-bottom: 10px;
}

.product-list-wrapper .single-product-list .content .price {
    display: block;
}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 575px) {

    .product-list-wrapper .single-product-list .thumb {
        flex: 0 0 100px;
        width: 100px;
        height: 100px;
    }

    .product-list-wrapper .single-product-list .thumb .image {
        width: 100px;
        height: 100px;
    }

    .product-list-wrapper .single-product-list .thumb img.first-image,
    .product-list-wrapper .single-product-list .thumb img.second-image {
        width: 100px !important;
        height: 100px !important;
        max-width: 100px !important;
        max-height: 100px !important;
        object-fit: cover !important;
    }

    .product-list-wrapper .single-product-list .content {
        padding-left: 15px;
    }

}

/* =========================================================
   PRODUCT SECTION - NEVER HIDE SINGLE IMAGE ON HOVER
========================================================= */

.product .thumb .image {
    display: block;
    position: relative;
    overflow: hidden;
}

.product .thumb .image img.first-image {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
    width: 100% !important;
    height: auto !important;
}

/* There is only ONE image, so keep it visible on hover */
.product .thumb:hover .first-image {
    opacity: 1 !important;
    visibility: visible !important;
}

/* Prevent theme hover from hiding the only image */
.product .thumb .image:only-child img.first-image {
    opacity: 1 !important;
    visibility: visible !important;
}
/* =========================================================
   PRODUCT LIST SWIPER - FAMILY DEALS
========================================================= */

.product-list-carousel-4 {
    position: relative;
    width: 100%;
}

.product-list-carousel-4 .swiper-container {
    width: 100%;
    overflow: hidden;
}

.product-list-carousel-4 .swiper-wrapper {
    display: flex;
}

.product-list-carousel-4 .swiper-slide {
    width: 100%;
    flex-shrink: 0;
}

/* Navigation buttons */
.product-list-carousel-4 .swiper-button-next,
.product-list-carousel-4 .swiper-button-prev {
    z-index: 10;
}

.product-list-carousel-4 .swiper-button-next {
    right: 0;
}

.product-list-carousel-4 .swiper-button-prev {
    left: 0;
}
    </style>
@endpush
@section('content')

  <!-- Hero Slider — static promotional copy for now; swap images/text here
       whenever you want, or later make this admin-editable the same way
       Deals are. -->
 <div class="section">
  <div class="hero-slider">
    <div class="swiper-container">
      <div class="swiper-wrapper">

        @forelse ($sliders ?? [] as $slide)
          <div class="hero-slide-item swiper-slide">
            <div class="hero-slide-bg">
              <img src="{{ $slide->image_url }}" alt="{{ $slide->title }}" />
            </div>
            <!-- <div class="container">
              <div class="hero-slide-content">
               
               
              </div>
            </div> -->
          </div>
        @empty
          {{-- No slides configured yet — fall back to these two defaults
               so the homepage never shows an empty/broken slider. --}}
          <div class="hero-slide-item swiper-slide">
            <div class="hero-slide-bg">
              <img src="{{ asset('storefront/assets/images/slider/slide-1.jpg') }}" alt="Slider Image" />
            </div>
            <div class="container">
              <div class="hero-slide-content">
                <h2 class="title">Fresh, Made<br />to Order</h2>
                <p>Explore today's menu — new dishes added every week</p>
                <a href="#menu" class="btn btn-lg btn-primary btn-hover-dark">View Menu</a>
              </div>
            </div>
          </div>

          <div class="hero-slide-item swiper-slide">
            <div class="hero-slide-bg">
              <img src="{{ asset('storefront/assets/images/slider/slide-1-2.jpg') }}" alt="Slider Image" />
            </div>
            <div class="container">
              <div class="hero-slide-content">
                <h2 class="title">Today's<br />Specials</h2>
                <p>Limited-time offers, while they last</p>
                <a href="#deals" class="btn btn-lg btn-primary btn-hover-dark">See Deals</a>
              </div>
            </div>
          </div>
        @endforelse

      </div>
      <div class="swiper-pagination d-md-none"></div>
      <div class="home-slider-prev swiper-button-prev main-slider-nav d-md-flex d-none"><i class="pe-7s-angle-left"></i></div>
      <div class="home-slider-next swiper-button-next main-slider-nav d-md-flex d-none"><i class="pe-7s-angle-right"></i></div>
    </div>
  </div>
</div>

<!-- Banner Section Start -->
<div class="section section-margin">
    <div class="container">

        <!-- Banners Start -->
        <div class="row mb-n6">

            @forelse ($categoryBanners ?? [] as $category)
                <!-- Banner Start -->
                <div class="col-lg-4 col-md-6 col-12 mb-6">
                    <div class="banner" data-aos="fade-up" data-aos-delay="{{ 300 + ($loop->index * 200) }}">
                        <div class="banner-image">
                            <a href="{{ route('storefront.category', $category->slug) }}">
                                <img src="{{ $category->media->image_url }}" alt="{{ $category->name }}">
                            </a>
                        </div>
                        <div class="info">
                            <div class="small-banner-content">
                                
                                @if ($category->media->hasPdfMenu())
                                    <a href="{{ $category->media->pdf_menu_url }}" target="_blank"
                                        class="btn btn-dark btn-sm">
                                        <i class="material-icons-outlined align-middle" style="font-size:14px;"></i>
                                        View PDF
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Banner End -->
            @empty
                {{-- No categories have a banner image configured yet — nothing
                     renders rather than showing broken/placeholder banners. --}}
            @endforelse

        </div>
        <!-- Banners End -->
    </div>
</div>
<!-- Banner Section End -->
 
<!-- Product Section Start -->
<div class="section section-padding mt-0">
    <div class="container">
        <!-- Section Title & Tab Start -->
        <div class="row">
            <!-- Tab Start -->
            <div class="col-12">
                <ul class="product-tab-nav nav justify-content-center mb-10 title-border-bottom mt-n3">
                    <li class="nav-item" data-aos="fade-up" data-aos-delay="300"><a class="nav-link active mt-3" data-bs-toggle="tab" href="#tab-product-all">All Menu</a></li>
                    <li class="nav-item" data-aos="fade-up" data-aos-delay="400"><a class="nav-link mt-3" data-bs-toggle="tab" href="#tab-product-featured">Featured Dishes</a></li>
                    <li class="nav-item" data-aos="fade-up" data-aos-delay="500"><a class="nav-link mt-3" data-bs-toggle="tab" href="#tab-product-family"> Deals</a></li>
                </ul>
            </div>
            <!-- Tab End -->
        </div>
        <!-- Section Title & Tab End -->

        <!-- Products Tab Start -->
        <div class="row">
            <div class="col">
                <div class="tab-content position-relative">

                    <!-- All Menu Tab -->
                    <div class="tab-pane fade show active" id="tab-product-all">
                        <div class="product-carousel">
                            <div class="swiper-container">
                                <div class="swiper-wrapper mb-n10">

                                    @php $delay = 300; @endphp
                                    @foreach ($newArrivals->chunk(2) as $pair)
                                        <!-- Product Start -->
                                        <div class="swiper-slide product-wrapper">
                                            @foreach ($pair as $item)
                                                <!-- Single Product Start -->
                                                <div class="product product-border-left mb-10" data-aos="fade-up" data-aos-delay="{{ $delay }}">
                                                    <div class="thumb">
                                                        <a href="{{ route('storefront.dish', $item->slug) }}" class="image">
                                                            <img class="first-image" src="{{ $item->image_url }}" alt="{{ $item->name }}" />
                                                        </a>
                                                        @if ($item->is_featured || $dealItemIds->contains($item->id))
                                                            <span class="badges">
                                                                @if ($dealItemIds->contains($item->id))
                                                                    <span class="sale">Deal</span>
                                                                @endif
                                                                @if ($item->is_featured)
                                                                    <span class="sale">Featured</span>
                                                                @endif
                                                            </span>
                                                        @endif
                                                        <div class="actions">
                                                            <a href="javascript:void(0)" class="action quickview" data-bs-toggle="modal" data-bs-target="#exampleModalCenter" data-slug="{{ $item->slug }}"><i class="pe-7s-search"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="content">
                                                        <h4 class="sub-title"><a href="{{ $item->category ? route('storefront.category', $item->category->slug) : '#' }}">{{ $item->category->name ?? 'Menu' }}</a></h4>
                                                        <h5 class="title"><a href="{{ route('storefront.dish', $item->slug) }}" class="text-uppercase">{{ $item->name }}</a></h5>
                                                        <span class="ratings">
                                                            <span class="rating-wrap">
                                                                <span class="star" style="width: 100%"></span>
                                                            </span>
                                                            <span class="rating-num">(5)</span>
                                                        </span>
                                                        <span class="price">
                                                            @if ($item->is_on_sale)
                                                                <span class="new">£{{ number_format($item->discount_price, 2) }}</span>
                                                                <span class="old">£{{ number_format($item->price, 2) }}</span>
                                                            @else
                                                                <span class="new">£{{ number_format($item->price, 2) }}</span>
                                                            @endif
                                                        </span>
                                                        @include('storefront.partials.add-to-cart-button', ['item' => $item])
                                                    </div>
                                                </div>
                                                <!-- Single Product End -->
                                                @php $delay += 100; @endphp
                                            @endforeach
                                        </div>
                                        <!-- Product End -->
                                    @endforeach

                                    @if ($newArrivals->isEmpty())
                                        <div class="swiper-slide product-wrapper">
                                            <p class="text-muted text-center w-100">No dishes available yet.</p>
                                        </div>
                                    @endif

                                </div>

                                <!-- Swiper Pagination Start -->
                                <div class="swiper-pagination d-md-none"></div>
                                <!-- Swiper Pagination End -->

                                <!-- Next Previous Button Start -->
                                <div class="swiper-product-button-next swiper-button-next swiper-button-white d-md-flex d-none"><i class="pe-7s-angle-right"></i></div>
                                <div class="swiper-product-button-prev swiper-button-prev swiper-button-white d-md-flex d-none"><i class="pe-7s-angle-left"></i></div>
                                <!-- Next Previous Button End -->
                            </div>
                        </div>
                    </div>

                    <!-- Featured Dishes Tab -->
                    <div class="tab-pane fade" id="tab-product-featured">
                        <div class="product-carousel">
                            <div class="swiper-container">
                                <div class="swiper-wrapper mb-n10">

                                    @php $delay = 300; @endphp
                                    @foreach ($featured->chunk(2) as $pair)
                                        <!-- Product Start -->
                                        <div class="swiper-slide product-wrapper">
                                            @foreach ($pair as $item)
                                                <!-- Single Product Start -->
                                                <div class="product product-border-left mb-10" data-aos="fade-up" data-aos-delay="{{ $delay }}">
                                                    <div class="thumb">
                                                        <a href="{{ route('storefront.dish', $item->slug) }}" class="image">
                                                            <img class="first-image" src="{{ $item->image_url }}" alt="{{ $item->name }}" />
                                                        </a>
                                                        @if ($item->is_featured || $dealItemIds->contains($item->id))
                                                            <span class="badges">
                                                                @if ($dealItemIds->contains($item->id))
                                                                    <span class="sale">Deal</span>
                                                                @endif
                                                                @if ($item->is_featured)
                                                                    <span class="sale">Featured</span>
                                                                @endif
                                                            </span>
                                                        @endif
                                                        <div class="actions">
                                                            <a href="javascript:void(0)" class="action quickview" data-bs-toggle="modal" data-bs-target="#exampleModalCenter" data-slug="{{ $item->slug }}"><i class="pe-7s-search"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="content">
                                                        <h4 class="sub-title"><a href="{{ $item->category ? route('storefront.category', $item->category->slug) : '#' }}">{{ $item->category->name ?? 'Menu' }}</a></h4>
                                                        <h5 class="title"><a href="{{ route('storefront.dish', $item->slug) }}" class="text-uppercase">{{ $item->name }}</a></h5>
                                                        <span class="ratings">
                                                            <span class="rating-wrap">
                                                                <span class="star" style="width: 100%"></span>
                                                            </span>
                                                            <span class="rating-num">(5)</span>
                                                        </span>
                                                        <span class="price">
                                                            @if ($item->is_on_sale)
                                                                <span class="new">£{{ number_format($item->discount_price, 2) }}</span>
                                                                <span class="old">£{{ number_format($item->price, 2) }}</span>
                                                            @else
                                                                <span class="new">£{{ number_format($item->price, 2) }}</span>
                                                            @endif
                                                        </span>
                                                        @include('storefront.partials.add-to-cart-button', ['item' => $item])
                                                    </div>
                                                </div>
                                                <!-- Single Product End -->
                                                @php $delay += 100; @endphp
                                            @endforeach
                                        </div>
                                        <!-- Product End -->
                                    @endforeach

                                    @if ($featured->isEmpty())
                                        <div class="swiper-slide product-wrapper">
                                            <p class="text-muted text-center w-100">No featured dishes yet.</p>
                                        </div>
                                    @endif

                                </div>

                                <!-- Swiper Pagination Start -->
                                <div class="swiper-pagination d-md-none"></div>
                                <!-- Swiper Pagination End -->

                                <!-- Next Previous Button Start -->
                                <div class="swiper-product-button-next swiper-button-next swiper-button-white d-md-flex d-none"><i class="pe-7s-angle-right"></i></div>
                                <div class="swiper-product-button-prev swiper-button-prev swiper-button-white d-md-flex d-none"><i class="pe-7s-angle-left"></i></div>
                                <!-- Next Previous Button End -->
                            </div>
                        </div>
                    </div>

                  <!-- Family Deals Tab -->
                    <div class="tab-pane fade" id="tab-product-family">
                        <div class="product-carousel">
                            <div class="swiper-container">
                                <div class="swiper-wrapper mb-n10">

                                    @php $delay = 300; @endphp
                                    @foreach ($familyDealProducts->chunk(2) as $pair)
                                        <!-- Product Start -->
                                        <div class="swiper-slide product-wrapper">
                                            @foreach ($pair as $deal)
                                                @php
                                                    // A combo/bundle deal is one fixed-price unit, not a
                                                    // per-item discount — so the card represents the
                                                    // whole bundle (thumbnail, item list, combo price),
                                                    // using the bundle-level helpers on the Deal model.
                                                    $bundlePrice = (float) $deal->combo_price;
                                                    $originalPrice = $deal->bundleOriginalPrice();

                                                    // No "add whole bundle to cart" action exists yet, so
                                                    // this falls back to the first component dish for the
                                                    // Add to Cart button — it's a stand-in, not really
                                                    // "add the combo". Wire this to a proper bundle-cart
                                                    // route once one exists.
                                                    //
                                                    // Quick View is different: HomeController now has a
                                                    // dedicated quickviewBundle(Deal $deal) endpoint, so
                                                    // the quickview link below targets the deal itself via
                                                    // data-deal-id, with $firstComponent->slug kept only as
                                                    // a fallback for JS that doesn't check for it yet.
                                                    $firstComponent = $deal->bundleComponents
                                                        ->first(fn ($c) => $c->menuItem)
                                                        ?->menuItem;
                                                @endphp
                                                <!-- Single Product Start -->
                                                <div class="product product-border-left mb-10" data-aos="fade-up" data-aos-delay="{{ $delay }}">
                                                    <div class="thumb">
                                                        <a href="{{ route('storefront.deals.index', ['type' => $deal->type]) }}" class="image">
                                                            <img class="first-image" src="{{ Storage::url($deal->image) }}" alt="{{ $deal->name }}" />
                                                        </a>
                                                        <span class="badges">
                                                            <span class="sale">Deal</span>
                                                        </span>
                                                        <div class="actions">
                                                            <a href="javascript:void(0)" class="action quickview" data-bs-toggle="modal" data-bs-target="#exampleModalCenter" data-slug="{{ $firstComponent?->slug }}" data-deal-id="{{ $deal->id }}"><i class="pe-7s-search"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="content">
                                                        <h4 class="sub-title">{{ \App\Models\Deal::typeLabel($deal->type) }}</h4>
                                                        <h5 class="title">
                                                            <a href="{{ route('storefront.deals.index', ['type' => $deal->type]) }}" class="text-uppercase">{{ $deal->name }}</a>
                                                        </h5>
                                                        <span class="ratings">
                                                            <span class="rating-wrap">
                                                                <span class="star" style="width: 100%"></span>
                                                            </span>
                                                            <span class="rating-num">(5)</span>
                                                        </span>
                                                        <p class="mb-2 small text-muted">{{ $deal->bundleItemNames() }}</p>
                                                        <span class="price">
                                                            @if ($originalPrice > $bundlePrice)
                                                                <span class="new">£{{ number_format($bundlePrice, 2) }}</span>
                                                                <span class="old">£{{ number_format($originalPrice, 2) }}</span>
                                                            @else
                                                                <span class="new">£{{ number_format($bundlePrice, 2) }}</span>
                                                            @endif
                                                        </span>
                                                        @if ($firstComponent)
                                                         @include('storefront.partials.add-bundle-to-cart-button', ['deal' => $deal])                                                        @endif
                                                    </div>
                                                </div>
                                                <!-- Single Product End -->
                                                @php $delay += 100; @endphp
                                            @endforeach
                                        </div>
                                        <!-- Product End -->
                                    @endforeach

                                    @if ($familyDealProducts->isEmpty())
                                        <div class="swiper-slide product-wrapper">
                                            <p class="text-muted text-center w-100">No family deals right now.</p>
                                        </div>
                                    @endif

                                </div>

                                <!-- Swiper Pagination Start -->
                                <div class="swiper-pagination d-md-none"></div>
                                <!-- Swiper Pagination End -->

                                <!-- Next Previous Button Start -->
                                <div class="swiper-product-button-next swiper-button-next swiper-button-white d-md-flex d-none"><i class="pe-7s-angle-right"></i></div>
                                <div class="swiper-product-button-prev swiper-button-prev swiper-button-white d-md-flex d-none"><i class="pe-7s-angle-left"></i></div>
                                <!-- Next Previous Button End -->
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- Products Tab End -->
    </div>
</div>
<!-- Product Section End -->

 <!-- Banner Fullwidth Start -->
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-12" data-aos="fade-up" data-aos-delay="300">
                    <div class="banner">
                        <div class="banner-image">
                            <a href="shop-grid.html"><img src="{{ asset('storefront/assets/images/banner/big-banner2.png') }}" alt="Banner"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Banner Fullwidth End -->

  <!-- Product Deal Section Start -->
<div class="section section-padding mt-0 overflow-hidden">
    <div class="container">

        @if ($dailyDealCards->isNotEmpty())
            <!-- Section Title & Tab Start -->
            <div class="row">
                <div class="col-12">
                    <div class="section-title-produt-tab-wrapper">
                        <div class="section-title m-0" data-aos="fade-right" data-aos-delay="300">
                            <h1 class="title">Daily Deals</h1>
                        </div>
                        <ul class="product-tab-nav nav mt-n3" data-aos="fade-left" data-aos-delay="300">
                            @foreach ($dailyDealCards as $type => $cards)
                                <li class="nav-item">
                                    <a class="nav-link mt-3 @if ($loop->first) active @endif"
                                        data-bs-toggle="tab" href="#product-deal-{{ $type }}">
                                        {{ \App\Models\Deal::typeLabel($type) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Section Title & Tab End -->

            <!-- Products Tab Start -->
            <div class="row">
                <div class="col">
                    <div class="tab-content position-relative">
                        @foreach ($dailyDealCards as $type => $cards)
                            <div class="tab-pane fade @if ($loop->first) show active @endif" id="product-deal-{{ $type }}">
                                <div class="product-deal-carousel">
                                    <div class="swiper-container">
                                        <div class="swiper-wrapper">

                                            @foreach ($cards as $card)
                                                @php
                                                    $deal = $card['deal'];
                                                    $item = $card['item'];
                                                    $discountedPrice = $deal->discountedPriceFor((float) $item->price);
                                                    $countdown = $deal->countdownTarget();
                                                @endphp

                                                <!-- Product Start -->
                                                <div class="swiper-slide product-wrapper">
                                                    <!-- Single Product Deal Start -->
                                                    <div class="product single-deal-product product-border-left">
                                                        <div class="thumb">
                                                            <a href="{{ route('storefront.dish', $item->slug) }}" class="image">
                                                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" />
                                                            </a>
                                                            @if ($deal->discountBadgeLabel())
                                                                <span class="badges">
                                                                    <span class="sale">{{ $deal->discountBadgeLabel() }}</span>
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="content">
                                                            <p class="inner-desc">Hurry Up! Offer Ends In:</p>
                                                            <div class="countdown-area">
                                                                @if ($countdown)
                                                                    <div class="countdown-wrapper d-flex" data-countdown="{{ $countdown->format('Y/m/d H:i:s') }}"></div>
                                                                @else
                                                                    <p class="mb-0 small text-muted">{{ $deal->scheduleSummary() }}</p>
                                                                @endif
                                                            </div>
                                                            <h4 class="sub-title"><a href="{{ $item->category ? route('storefront.category', $item->category->slug) : '#' }}">{{ $item->category->name ?? 'Menu' }}</a></h4>
                                                            <h5 class="title"><a href="{{ route('storefront.dish', $item->slug) }}">{{ $item->name }}</a></h5>
                                                            <span class="price">
                                                                <span class="new">£{{ number_format($discountedPrice, 2) }}</span>
                                                                <span class="old">£{{ number_format($item->price, 2) }}</span>
                                                            </span>
                                                            @include('storefront.partials.add-to-cart-button', ['item' => $item, 'deal' => $deal])
                                                        </div>
                                                    </div>
                                                    <!-- Single Product Deal End -->
                                                </div>
                                                <!-- Product End -->
                                            @endforeach

                                        </div>

                                        <!-- Swiper Pagination Start -->
                                        <div class="swiper-pagination d-md-none"></div>
                                        <!-- Swiper Pagination End -->

                                        <!-- Next Previous Button Start -->
                                        <div class="swiper-product-deal-next swiper-button-next swiper-button-white d-md-flex d-none"><i class="pe-7s-angle-right"></i></div>
                                        <div class="swiper-product-deal-prev swiper-button-prev swiper-button-white d-md-flex d-none"><i class="pe-7s-angle-left"></i></div>
                                        <!-- Next Previous Button End -->
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- Products Tab End -->
        @endif

    </div>
</div>
<!-- Product List Start -->
<div class="section section-padding jp-product-list-section">
    <div class="container">
        <div class="row mb-n8">

            {{-- =========================================================
                 FEATURED DISHES
            ========================================================== --}}
            <div class="col-md-6 col-lg-4 col-12 mb-8" data-aos="fade-up" data-aos-delay="300">
                <div class="product-list-title">
                    <h2 class="title pb-3 mb-0">Featured Dishes</h2>
                    <span></span>
                </div>

                <div class="product-list-carousel">
                    <div class="swiper-container">
                        <div class="swiper-wrapper">

                            @forelse($featured->chunk(3) as $products)
                                <div class="swiper-slide product-list-wrapper">
                                    @foreach($products as $item)
                                        <div class="single-product-list jp-single-product product-hover mb-6">
                                            <div class="thumb">
                                                <a href="{{ route('storefront.dish', $item->slug) }}" class="image">
                                                    <img class="first-image" src="{{ $item->image_url }}" alt="{{ $item->name }}">
                                                </a>
                                            </div>
                                            <div class="content">
                                                <h5 class="title">
                                                    <a href="{{ route('storefront.dish', $item->slug) }}">{{ $item->name }}</a>
                                                </h5>
                                                <span class="price">
                                                    @if(!is_null($item->discount_price))
                                                        <span class="new">£{{ number_format((float)$item->discount_price, 2) }}</span>
                                                        @if(!is_null($item->price))
                                                            <span class="old">£{{ number_format((float)$item->price, 2) }}</span>
                                                        @endif
                                                    @else
                                                        <span class="new">£{{ number_format((float)$item->price, 2) }}</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @empty
                                <div class="swiper-slide product-list-wrapper">
                                    <p class="text-muted">No featured dishes available.</p>
                                </div>
                            @endforelse

                        </div>

                        <div class="swiper-product-list-next swiper-button-next"><i class="pe-7s-angle-right"></i></div>
                        <div class="swiper-product-list-prev swiper-button-prev"><i class="pe-7s-angle-left"></i></div>
                    </div>
                </div>
            </div>

            {{-- =========================================================
                 NEW DISHES
            ========================================================== --}}
            <div class="col-md-6 col-lg-4 col-12 mb-8" data-aos="fade-up" data-aos-delay="400">
                <div class="product-list-title">
                    <h2 class="title pb-3 mb-0">New Dishes</h2>
                    <span></span>
                </div>

                <div class="product-list-carousel-2">
                    <div class="swiper-container">
                        <div class="swiper-wrapper">

                            @forelse($newArrivals->chunk(3) as $products)
                                <div class="swiper-slide product-list-wrapper">
                                    @foreach($products as $item)
                                        <div class="single-product-list jp-single-product product-hover mb-6">
                                            <div class="thumb">
                                                <a href="{{ route('storefront.dish', $item->slug) }}" class="image">
                                                    <img class="first-image" src="{{ $item->image_url }}" alt="{{ $item->name }}">
                                                </a>
                                            </div>
                                            <div class="content">
                                                <h5 class="title">
                                                    <a href="{{ route('storefront.dish', $item->slug) }}">{{ $item->name }}</a>
                                                </h5>
                                                <span class="price">
                                                    @if(!is_null($item->discount_price))
                                                        <span class="new">£{{ number_format((float)$item->discount_price, 2) }}</span>
                                                        <span class="old">£{{ number_format((float)$item->price, 2) }}</span>
                                                    @else
                                                        <span class="new">£{{ number_format((float)$item->price, 2) }}</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @empty
                                <div class="swiper-slide product-list-wrapper">
                                    <p class="text-muted">No new dishes available.</p>
                                </div>
                            @endforelse

                        </div>

                        <div class="swiper-product-list-next swiper-button-next"><i class="pe-7s-angle-right"></i></div>
                        <div class="swiper-product-list-prev swiper-button-prev"><i class="pe-7s-angle-left"></i></div>
                    </div>
                </div>
            </div>

            {{-- =========================================================
                 ON SALE
            ========================================================== --}}
            <div class="col-md-6 col-lg-4 col-12 mb-8" data-aos="fade-up" data-aos-delay="500">
                <div class="product-list-title">
                    <h2 class="title pb-3 mb-0">Best Sale</h2>
                    <span></span>
                </div>

                <div class="product-list-carousel-3">
                    <div class="swiper-container">
                        <div class="swiper-wrapper">

                            @forelse($onSale->chunk(3) as $products)
                                <div class="swiper-slide product-list-wrapper">
                                    @foreach($products as $item)
                                        <div class="single-product-list jp-single-product product-hover mb-6">
                                            <div class="thumb">
                                                <a href="{{ route('storefront.dish', $item->slug) }}" class="image">
                                                    <img class="first-image" src="{{ $item->image_url }}" alt="{{ $item->name }}">
                                                </a>

                                                {{-- SALE BADGE --}}
                                                <span class="badges">
                                                    <span class="sale">Sale</span>
                                                </span>
                                            </div>
                                            <div class="content">
                                                <h5 class="title">
                                                    <a href="{{ route('storefront.dish', $item->slug) }}">{{ $item->name }}</a>
                                                </h5>
                                                <span class="price">
                                                    <span class="new">£{{ number_format((float)$item->discount_price, 2) }}</span>
                                                    @if(!is_null($item->price))
                                                        <span class="old">£{{ number_format((float)$item->price, 2) }}</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @empty
                                <div class="swiper-slide product-list-wrapper">
                                    <p class="text-muted">No sale dishes available.</p>
                                </div>
                            @endforelse

                        </div>

                        <div class="swiper-product-list-next swiper-button-next"><i class="pe-7s-angle-right"></i></div>
                        <div class="swiper-product-list-prev swiper-button-prev"><i class="pe-7s-angle-left"></i></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- Product List End -->
@endsection

@push('scripts')

@endpush