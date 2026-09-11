@extends('storefront.layouts.app')

@section('title', $item->name . ' — ' . config('app.name', 'Restaurant'))

@push('styles')
<style>
    #dish-customizations-list label.badge {
        position: relative;
        transition: background-color .15s ease, border-color .15s ease, color .15s ease;
    }

    #dish-customizations-list label.badge input {
        position: absolute;
        opacity: 0;
        width: 1px;
        height: 1px;
        pointer-events: none;
    }

    #dish-customizations-list label.badge.selected {
        background-color: #b22b40 !important;
        border-color: #b22b40 !important;
        color: #fff !important;
    }

    #dish-customizations-list label.badge.selected .text-danger {
        color: #fff !important;
    }
</style>
@endpush

@section('content')

  <!-- Breadcrumb Section Start -->
  
  <!-- Breadcrumb Section End -->

  <!-- Shop Section Start -->
  <div class="section section-margin">
    <div class="container">

      <div class="row">
        <div class="col-lg-5 offset-lg-0 col-md-8 offset-md-2 col-custom">

          <!-- Product Details Image Start -->
          <div class="product-details-img">

            <!-- Single Product Image Start -->
            <div class="single-product-img swiper-container gallery-top">
              <div class="swiper-wrapper popup-gallery">
                @forelse ($item->images as $image)
                  <a class="swiper-slide w-100" href="{{ $image->url }}">
                    <img class="w-100" src="{{ $image->url }}" alt="{{ $item->name }}">
                  </a>
                @empty
                  <a class="swiper-slide w-100" href="{{ $item->image_url }}">
                    <img class="w-100" src="{{ $item->image_url }}" alt="{{ $item->name }}">
                  </a>
                @endforelse
              </div>
            </div>
            <!-- Single Product Image End -->

            @if ($item->images->count() > 1)
              <!-- Single Product Thumb Start -->
              <div class="single-product-thumb swiper-container gallery-thumbs">
                <div class="swiper-wrapper">
                  @foreach ($item->images as $image)
                    <div class="swiper-slide">
                      <img src="{{ $image->url }}" alt="{{ $item->name }}">
                    </div>
                  @endforeach
                </div>

                <div class="swiper-button-next swiper-button-white"><i class="pe-7s-angle-right"></i></div>
                <div class="swiper-button-prev swiper-button-white"><i class="pe-7s-angle-left"></i></div>
              </div>
              <!-- Single Product Thumb End -->
            @endif

          </div>
          <!-- Product Details Image End -->

        </div>
        <div class="col-lg-7 col-custom">

          <!-- Product Summery Start -->
          <div class="product-summery position-relative">

            <!-- Product Head Start -->
            <div class="product-head mb-3">
              <h2 class="product-title">{{ $item->name }}</h2>
            </div>
            <!-- Product Head End -->

            <!-- Price Box Start -->
            <div class="price-box mb-2" id="dish-price-box">
              <!-- populated by JS on load, so it can live-update with customizations -->
            </div>
            <!-- Price Box End -->

            <!-- Rating Start -->
            <span class="ratings justify-content-start">
              <span class="rating-wrap">
                <span class="star" style="width: 100%"></span>
              </span>
              <span class="rating-num">(5)</span>
            </span>
            <!-- Rating End -->

            <!-- SKU Start -->
            <div class="sku mb-3">
              <span>Ref: {{ $item->slug }}</span>
            </div>
            <!-- SKU End -->

            <!-- Description Start -->
            @if ($item->description)
              <p class="desc-content mb-5">{{ $item->description }}</p>
            @endif
            <!-- Description End -->

            @if ($deal && $deal->countdownTarget())
              <!-- Deal Countdown Start -->
              <div class="countdown-area mb-6">
                <p class="inner-desc mb-1">Hurry Up! Offer Ends In:</p>
                <div class="countdown-wrapper d-flex" data-countdown="{{ $deal->countdownTarget()->format('Y/m/d H:i:s') }}"></div>
              </div>
              <!-- Deal Countdown End -->
            @endif

            @if ($item->spice_level !== 'none')
              <!-- Spice Level -->
              <div class="product-meta mb-3">
                <span>Spice Level :</span>
                <a href="javascript:void(0)"><strong>{{ ucfirst($item->spice_level) }}</strong></a>
              </div>
            @endif

            @php
              $dietary = array_filter([
                  $item->is_vegetarian ? 'Vegetarian' : null,
                  $item->is_vegan ? 'Vegan' : null,
                  $item->is_gluten_free ? 'Gluten-Free' : null,
              ]);
            @endphp
            @if (!empty($dietary))
              <!-- Dietary Tags -->
              <div class="product-meta mb-5">
                <span>Dietary :</span>
                <span>{{ implode(', ', $dietary) }}</span>
              </div>
            @endif

            @if ($item->optionGroups->isNotEmpty())
              <!-- Customizations Start -->
              <div class="product-meta mb-5 d-block">
                <span class="d-block mb-2">Customize Your Order :</span>
                <div id="dish-customizations-list">
                  @foreach ($item->optionGroups as $group)
                    @php
                      $inputType = $group->selection_type === 'multiple' ? 'checkbox' : 'radio';
                      $groupName = 'dish_group_'.$group->id;
                      $isRequired = $group->min_select > 0;
                    @endphp
                    <div class="mb-3">
                      <strong class="d-block">
                        {{ $group->name }}
                        <span class="text-muted small fw-normal">
                          ({{ $group->selection_type === 'single' ? 'choose one' : 'choose multiple' }}{{ $isRequired ? ', required' : '' }})
                        </span>
                      </strong>
                      <div class="d-flex flex-wrap gap-2 mt-2">
                        @foreach ($group->values as $index => $value)
                          @php
                            $inputId = 'dish_opt_'.$group->id.'_'.$value->id;
                            $checked = ($inputType === 'radio' && $isRequired && $index === 0);
                          @endphp
                          <label class="badge bg-light text-dark border px-3 py-2 rounded-pill {{ $checked ? 'selected' : '' }}" for="{{ $inputId }}" style="cursor:pointer;">
                            <input type="{{ $inputType }}" name="{{ $groupName }}" id="{{ $inputId }}"
                              value="{{ $value->id }}"
                              data-price-delta="{{ $value->price_delta }}"
                              @checked($checked)
                              {{ $inputType === 'radio' && $isRequired ? 'required' : '' }}>
                            {{ $value->name }}
                            @if ($value->price_delta > 0)
                              <span class="text-danger">+£{{ number_format($value->price_delta, 2) }}</span>
                            @endif
                          </label>
                        @endforeach
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
              <!-- Customizations End -->
            @endif

            <!-- Quantity Start -->
            <div class="quantity mb-5">
              <div class="cart-plus-minus">
                <input class="cart-plus-minus-box" id="dish-quantity" value="1" type="text">
                <div class="dec qtybutton">-</div>
                <div class="inc qtybutton">+</div>
              </div>
            </div>
            <!-- Quantity End -->

            <!-- Cart & Wishlist Button Start -->
            <div class="cart-wishlist-btn mb-4">
              <div class="add-to_cart">
                @include('storefront.partials.add-to-cart-button', [
                    'item' => $item,
                    'optionsContainer' => 'dish-customizations-list',
                    'quantityInput' => 'dish-quantity',
                ])
              </div>
              <!-- <div class="add-to-wishlist">
                <a class="btn btn-outline-dark btn-hover-primary" href="{{ route('storefront.wishlist') }}">Add to Wishlist</a>
              </div> -->
            </div>
            <!-- Cart & Wishlist Button End -->

            <!-- Social Shear Start -->
            <div class="social-share">
              <span>Share :</span>
              <a href="#"><i class="fa fa-facebook-square facebook-color"></i></a>
              <a href="#"><i class="fa fa-twitter-square twitter-color"></i></a>
              <a href="#"><i class="fa fa-linkedin-square linkedin-color"></i></a>
              <a href="#"><i class="fa fa-pinterest-square pinterest-color"></i></a>
            </div>
            <!-- Social Shear End -->

            <!-- Product Delivery Policy Start -->
            <ul class="product-delivery-policy border-top pt-4 mt-4 border-bottom pb-4">
              <li><i class="fa fa-check-square"></i> <span>Freshly prepared to order</span></li>
              <li><i class="fa fa-truck"></i><span>Dine-in, pickup, or delivery</span></li>
              <li><i class="fa fa-refresh"></i><span>Ask us about allergens on request</span></li>
            </ul>
            <!-- Product Delivery Policy End -->

          </div>
          <!-- Product Summery End -->

        </div>
      </div>

      <div class="row section-margin">
        <!-- Single Product Tab Start -->
        <div class="col-lg-12 col-custom single-product-tab">
          <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active text-uppercase" id="home-tab" data-bs-toggle="tab" href="#connect-1" role="tab" aria-selected="true">Description</a>
            </li>
            @if ($item->ingredients)
              <li class="nav-item">
                <a class="nav-link text-uppercase" id="ingredients-tab" data-bs-toggle="tab" href="#connect-2" role="tab" aria-selected="false">Ingredients</a>
              </li>
            @endif
            <li class="nav-item">
              <a class="nav-link text-uppercase" id="contact-tab" data-bs-toggle="tab" href="#connect-3" role="tab" aria-selected="false">Delivery &amp; Policy</a>
            </li>
          </ul>
          <div class="tab-content mb-text" id="myTabContent">
            <div class="tab-pane fade show active" id="connect-1" role="tabpanel" aria-labelledby="home-tab">
              <div class="desc-content border p-3">
                <p class="mb-0">{{ $item->description ?: 'No further description available for this dish yet.' }}</p>
              </div>
            </div>
            @if ($item->ingredients)
              <div class="tab-pane fade" id="connect-2" role="tabpanel" aria-labelledby="ingredients-tab">
                <div class="desc-content border p-3">
                  <p class="mb-0">{{ $item->ingredients }}</p>
                </div>
              </div>
            @endif
            <div class="tab-pane fade" id="connect-3" role="tabpanel" aria-labelledby="contact-tab">
              <div class="shipping-policy mb-n2 border p-3">
                <h4 class="title-3 mb-4">Delivery &amp; Ordering</h4>
                <p class="desc-content mb-2">
                  This dish is prepared fresh to order and available for dine-in, pickup, or delivery.
                  Preparation time is typically {{ $item->prep_time_minutes ? $item->prep_time_minutes.' minutes' : 'a short wait' }}.
                </p>
                <ul class="policy-list mb-2">
                  <li>Freshly prepared once your order is placed</li>
                  <li>Ask a member of staff about allergens or dietary substitutions</li>
                  <li>Delivery times may vary based on demand and distance</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <!-- Single Product Tab End -->
      </div>

      @if ($related->isNotEmpty())
        <!-- Products Start -->
        <div class="row">

          <div class="col-12">
            <div class="section-title" data-aos="fade-up" data-aos-delay="300">
              <h2 class="title pb-3">You Might Also Like</h2>
              <span></span>
              <div class="title-border-bottom"></div>
            </div>
          </div>

          <div class="col">
            <div class="product-carousel">
              <div class="swiper-container">
                <div class="swiper-wrapper">

                  @foreach ($related as $relatedItem)
                    @php
                      $relatedImageUrl = $relatedItem->primaryImage?->url
                          ?? $relatedItem->images?->first()?->url
                          ?? $relatedItem->image_url;

                      $relatedDeal = $dealsByItemId->get($relatedItem->id);
                      $relatedCountdown = $relatedDeal?->countdownTarget();
                    @endphp
                    <div class="swiper-slide product-wrapper">
                      <div class="product product-border-left">
                        <div class="thumb">
                          <a href="{{ route('storefront.dish', $relatedItem->slug) }}" class="image">
                            <img class="first-image" src="{{ $relatedImageUrl }}" alt="{{ $relatedItem->name }}" />
                          </a>
                          @if ($relatedItem->is_featured || $relatedDeal)
                            <span class="badges">
                              @if ($relatedDeal)
                                <span class="sale">Deal</span>
                              @endif
                              @if ($relatedItem->is_featured)
                                <span class="sale">Featured</span>
                              @endif
                            </span>
                          @endif
                          <div class="actions">
                            <a href="{{ route('storefront.wishlist') }}" class="action wishlist"><i class="pe-7s-like"></i></a>
                            <a href="javascript:void(0)" class="action quickview" data-bs-toggle="modal" data-bs-target="#exampleModalCenter" data-slug="{{ $relatedItem->slug }}"><i class="pe-7s-search"></i></a>
                          </div>
                        </div>
                        <div class="content">
                          <h4 class="sub-title"><a href="{{ $relatedItem->category ? route('storefront.category', $relatedItem->category->slug) : '#' }}">{{ $relatedItem->category->name ?? 'Menu' }}</a></h4>
                          <h5 class="title"><a href="{{ route('storefront.dish', $relatedItem->slug) }}">{{ $relatedItem->name }}</a></h5>

                          {{-- Countdown only renders when this dish's deal actually
                               has a real end time (a one-off flash deal, or a
                               recurring window with a daily_end_time) — a plain
                               sale or an always-on deal type shows no countdown. --}}
                          @if ($relatedCountdown)
                            <div class="countdown-area mb-2">
                              <div class="countdown-wrapper d-flex" data-countdown="{{ $relatedCountdown->format('Y/m/d H:i:s') }}"></div>
                            </div>
                          @endif

                          <span class="ratings">
                            <span class="rating-wrap">
                              <span class="star" style="width: 100%"></span>
                            </span>
                            <span class="rating-num">(5)</span>
                          </span>
                          <span class="price">
                            @if ($relatedDeal)
                              <span class="new">£{{ number_format($relatedDeal->discountedPriceFor((float) $relatedItem->price), 2) }}</span>
                              <span class="old">£{{ number_format($relatedItem->price, 2) }}</span>
                            @elseif ($relatedItem->is_on_sale)
                              <span class="new">£{{ number_format($relatedItem->discount_price, 2) }}</span>
                              <span class="old">£{{ number_format($relatedItem->price, 2) }}</span>
                            @else
                              <span class="new">£{{ number_format($relatedItem->price, 2) }}</span>
                            @endif
                          </span>
                          @include('storefront.partials.add-to-cart-button', ['item' => $relatedItem])
                        </div>
                      </div>
                    </div>
                  @endforeach

                </div>

                <div class="swiper-pagination d-md-none"></div>
                <div class="swiper-product-button-next swiper-button-next swiper-button-white d-md-flex d-none"><i class="pe-7s-angle-right"></i></div>
                <div class="swiper-product-button-prev swiper-button-prev swiper-button-white d-md-flex d-none"><i class="pe-7s-angle-left"></i></div>
              </div>
            </div>
          </div>

        </div>
        <!-- Products End -->
      @endif

    </div>
  </div>
  <!-- Shop Section End -->

@endsection

@push('scripts')
<script>
  $(document).on('click', '.qtybutton', function () {

    const $box = $(this).closest('.cart-plus-minus').find('.cart-plus-minus-box');
    let qty = parseInt($box.val(), 10) || 1;

    qty = $(this).hasClass('inc') ? qty + 1 : Math.max(1, qty - 1);

    $box.val(qty).trigger('change');

});
(function ($) {
    "use strict";

    const basePrice = {{ $deal ? $deal->discountedPriceFor((float) $item->price) : ($item->is_on_sale ? (float) $item->discount_price : (float) $item->price) }};
    const oldPrice = {{ ($deal || $item->is_on_sale) ? (float) $item->price : 'null' }};

    function renderDishPrice() {
        let total = basePrice;

        $('#dish-customizations-list input:checked').each(function () {
            total += parseFloat($(this).data('price-delta')) || 0;
        });

        let html = `<span class="regular-price">£${total.toFixed(2)}</span>`;
        if (oldPrice !== null) {
            html += `<span class="old-price"><del>£${oldPrice.toFixed(2)}</del></span>`;
        }
        $('#dish-price-box').html(html);
    }

    $('#dish-customizations-list').on('change', 'input', function () {
        const $input = $(this);
        const $label = $input.closest('label.badge');

        if ($input.attr('type') === 'radio') {
            $label.closest('.d-flex').find('label.badge').removeClass('selected');
            $label.addClass('selected');
        } else {
            $label.toggleClass('selected', $input.prop('checked'));
        }

        renderDishPrice();
    });

    renderDishPrice();

})(jQuery);
</script>

<script>
    // Countdown builder/ticker — was missing from this page entirely.
    // Same logic already running correctly on deals.blade.php / home.blade.php.
    document.addEventListener('DOMContentLoaded', function () {
        const wrappers = document.querySelectorAll('[data-countdown]');
        if (! wrappers.length) return;

        function buildSkeleton(el) {
            el.innerHTML =
                '<div class="single-countdown"><span class="single-countdown_time cd-days">00</span><span class="single-countdown_text">Days</span></div>' +
                '<div class="single-countdown"><span class="single-countdown_time cd-hours">00</span><span class="single-countdown_text">Hours</span></div>' +
                '<div class="single-countdown"><span class="single-countdown_time cd-mins">00</span><span class="single-countdown_text">Min</span></div>' +
                '<div class="single-countdown"><span class="single-countdown_time cd-secs">00</span><span class="single-countdown_text">Sec</span></div>';
        }

        function parseCountdownDate(str) {
            const parts = str.split(' ');
            const datePart = parts[0];
            const timePart = parts[1] || '00:00:00';

            const [y, m, d] = datePart.split('/').map(Number);
            const [hh, mm, ss] = timePart.split(':').map(Number);

            return new Date(y, m - 1, d, hh || 0, mm || 0, ss || 0);
        }

        wrappers.forEach(buildSkeleton);

        function tick() {
            wrappers.forEach(function (el) {
                const target = parseCountdownDate(el.dataset.countdown);
                const diff = Math.max(0, target.getTime() - Date.now());

                const days = Math.floor(diff / 86400000);
                const hours = Math.floor((diff % 86400000) / 3600000);
                const mins = Math.floor((diff % 3600000) / 60000);
                const secs = Math.floor((diff % 60000) / 1000);

                const pad = n => String(n).padStart(2, '0');

                const daysEl = el.querySelector('.cd-days');
                const hoursEl = el.querySelector('.cd-hours');
                const minsEl = el.querySelector('.cd-mins');
                const secsEl = el.querySelector('.cd-secs');

                if (daysEl) daysEl.textContent = pad(days);
                if (hoursEl) hoursEl.textContent = pad(hours);
                if (minsEl) minsEl.textContent = pad(mins);
                if (secsEl) secsEl.textContent = pad(secs);
            });
        }

        tick();
        setInterval(tick, 1000);
    });
</script>
@endpush