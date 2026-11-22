@push('styles')
<style>
    .mega-menu-auto {
        display: flex;
        flex-wrap: wrap;
        gap: 40px;
        width: 100%;
        padding: 30px 40px;
        max-height: 70vh;
        overflow-y: auto;
    }

    .mega-menu-auto > .col {
        flex: 0 0 auto;
        width: auto;
        white-space: normal;
    }

    .mega-menu-auto .mega-menu-title {
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .02em;
        margin-bottom: 12px;
        position: relative;
        padding-bottom: 8px;
    }

    .mega-menu-auto .mega-menu-title::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 28px;
        height: 2px;
        background: var(--bs-primary, #2fb35b);
    }

    .mega-menu-auto ul.mb-n2 {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .mega-menu-auto ul.mb-n2 li {
        margin-bottom: 10px;
    }

    .mega-menu-auto ul.mb-n2 li a {
        color: #333;
        font-size: 14px;
        transition: color .15s ease;
        white-space: normal;
    }

    .mega-menu-auto ul.mb-n2 li a:hover {
        color: var(--bs-primary, #2fb35b);
    }
</style>



@endpush


{{--
    Full-screen site preloader.

    Include it as the FIRST thing inside <body> in storefront/layouts/app.blade.php:

        @include('storefront.partials.preloader')

    It covers the page until everything has loaded, then fades out and removes
    itself. Three things make sure it can never trap a visitor behind it:
      1. the script hides it on "load" (after a short minimum, so it never just flashes);
      2. the script hides it after 6s no matter what (a slow image shouldn't hold the page);
      3. a pure-CSS fallback hides it after 6.5s even if the script never runs at all.
--}}
@php
    $preloaderSettings = \App\Models\Setting::current();

    $preloaderLogo = ! empty($preloaderSettings?->logo)
        ? \Illuminate\Support\Facades\Storage::url($preloaderSettings->logo)
        : asset('storefront/assets/images/logo/logo.png');

    $preloaderName = $preloaderSettings?->site_name ?: config('app.name', 'Restaurant');
@endphp

<style>
    #site-preloader {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        opacity: 1;
        visibility: visible;
        transition: opacity .5s ease, visibility .5s ease;
        /* CSS-only failsafe: hides the overlay even if the script below never runs. */
        animation: sp-failsafe .01s linear 6.5s forwards;
    }

    #site-preloader.is-hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    @keyframes sp-failsafe {
        to { opacity: 0; visibility: hidden; pointer-events: none; }
    }

    #site-preloader .sp-inner {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
        padding: 24px;
        text-align: center;
    }

    #site-preloader .sp-logo {
        display: block;
        max-width: 190px;
        max-height: 72px;
        width: auto;
        height: auto;
        animation: sp-pulse 2.4s ease-in-out infinite;
    }

    #site-preloader .sp-flame {
        position: relative;
        width: 76px;
        height: 99px;
    }

    #site-preloader .sp-flame svg {
        display: block;
        width: 100%;
        height: 100%;
        overflow: visible;
    }

    /* Each layer flickers on its own rhythm, so the flame looks alive
    
       instead of pulsing as one block. */
    #site-preloader .sp-f {
        transform-box: fill-box;
        transform-origin: 50% 100%;
    }
    #site-preloader .sp-f1 { animation: sp-flicker 1.15s ease-in-out infinite; }
    #site-preloader .sp-f2 { animation: sp-flicker 0.85s ease-in-out infinite reverse; }
    #site-preloader .sp-f3 { animation: sp-flicker 0.65s ease-in-out infinite; }

    @keyframes sp-flicker {
        0%, 100% { transform: scale(1, 1) rotate(0deg); }
        25%      { transform: scale(.96, 1.06) rotate(-2deg); }
        50%      { transform: scale(1.03, .96) rotate(1.5deg); }
        75%      { transform: scale(.98, 1.04) rotate(-1deg); }
    }

    #site-preloader .sp-ember {
        position: absolute;
        bottom: 30px;
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #ff8a1f;
        opacity: 0;
        animation: sp-ember 1.8s ease-out infinite;
    }
    #site-preloader .sp-e1 { left: 18px; animation-delay: 0s; }
    #site-preloader .sp-e2 { left: 36px; animation-delay: .6s; width: 4px; height: 4px; background: #ffb703; }
    #site-preloader .sp-e3 { left: 54px; animation-delay: 1.1s; }
    #site-preloader .sp-e4 { left: 28px; animation-delay: 1.4s; width: 3px; height: 3px; background: #D12026; }

    @keyframes sp-ember {
        0%   { transform: translateY(0) scale(1); opacity: 0; }
        15%  { opacity: .9; }
        100% { transform: translateY(-78px) scale(.2); opacity: 0; }
    }

    #site-preloader .sp-caption {
        margin: 0;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: #6b7280;
    }

    #site-preloader .sp-bar {
        width: 150px;
        height: 3px;
        border-radius: 3px;
        background: #f3e3e3;
        overflow: hidden;
    }

    #site-preloader .sp-bar span {
        display: block;
        width: 40%;
        height: 100%;
        border-radius: 3px;
        background: linear-gradient(90deg, #D12026, #ff8a1f);
        animation: sp-slide 1.2s ease-in-out infinite;
    }

    @keyframes sp-slide {
        0%   { transform: translateX(-100%); }
        100% { transform: translateX(260%); }
    }

    @keyframes sp-pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%      { opacity: .82; transform: scale(.97); }
    }

    /* Respect "reduce motion": keep the picture, drop the movement.
       (Selector targets descendants only, so the CSS failsafe on the
       overlay itself keeps working.) */
    @media (prefers-reduced-motion: reduce) {
        #site-preloader .sp-inner * { animation: none !important; }
        #site-preloader .sp-bar span { width: 100%; }
        #site-preloader .sp-ember { display: none; }
    }
</style>

<div id="site-preloader" role="status" aria-live="polite" aria-label="Loading {{ $preloaderName }}">
    <div class="sp-inner">

        <img class="sp-logo" src="{{ $preloaderLogo }}" alt="{{ $preloaderName }}" onerror="this.style.display='none'">

        <div class="sp-flame" aria-hidden="true">
            <span class="sp-ember sp-e1"></span>
            <span class="sp-ember sp-e2"></span>
            <span class="sp-ember sp-e3"></span>
            <span class="sp-ember sp-e4"></span>

            <svg viewBox="0 0 100 130" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="sp-g1" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0" stop-color="#ff5a1f"/><stop offset="1" stop-color="#D12026"/>
                    </linearGradient>
                    <linearGradient id="sp-g2" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0" stop-color="#ffb703"/><stop offset="1" stop-color="#ff7a18"/>
                    </linearGradient>
                    <linearGradient id="sp-g3" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0" stop-color="#fff3b0"/><stop offset="1" stop-color="#ffd54a"/>
                    </linearGradient>
                </defs>
                <path class="sp-f sp-f1" fill="url(#sp-g1)" d="M50 4 C54 26 80 40 80 76 C80 104 66 124 50 124 C34 124 20 104 20 76 C20 58 30 48 36 36 C38 46 44 50 46 50 C44 34 46 16 50 4 Z"/>
                <path class="sp-f sp-f2" fill="url(#sp-g2)" d="M50 40 C53 56 68 64 68 88 C68 106 60 120 50 120 C40 120 32 106 32 88 C32 76 38 70 42 62 C43 68 47 70 48 70 C47 60 48 50 50 40 Z"/>
                <path class="sp-f sp-f3" fill="url(#sp-g3)" d="M50 74 C52 84 59 89 59 100 C59 110 55 118 50 118 C45 118 41 110 41 100 C41 94 45 90 47 86 C48 90 50 91 50 91 C49 84 50 79 50 74 Z"/>
            </svg>
        </div>

        <p class="sp-caption">Madras Cafe</p>
        <div class="sp-bar" aria-hidden="true"><span></span></div>

    </div>
</div>

{{-- Without JavaScript the script below can't run, so don't show it at all. --}}
<noscript><style>#site-preloader { display: none !important; }</style></noscript>

<script>
(function () {
    var overlay = document.getElementById('site-preloader');
    if (! overlay) return;

    var MIN_MS = 700;    // stay up at least this long, so a fast load doesn't just flash
    var MAX_MS = 6000;   // never keep the visitor waiting longer than this

    var shownAt = Date.now();
    var hidden  = false;

    function hide() {
        if (hidden) return;
        hidden = true;

        overlay.classList.add('is-hidden');

        // Remove it once the fade has finished, so it can't catch clicks or linger in the DOM.
        setTimeout(function () {
            if (overlay.parentNode) overlay.parentNode.removeChild(overlay);
        }, 700);
    }

    function pageLoaded() {
        var remaining = Math.max(0, MIN_MS - (Date.now() - shownAt));
        setTimeout(hide, remaining);
    }

    if (document.readyState === 'complete') {
        pageLoaded();
    } else {
        window.addEventListener('load', pageLoaded);
    }

    // Failsafe: a slow image or a stalled request must not hold the page hostage.
    setTimeout(hide, MAX_MS);

    // Back/forward button: the browser may restore the page from its cache in
    // whatever state it was left, so make sure the overlay isn't showing.
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) hide();
    });
})();
</script>

<div class="header section">

    <!-- Header Top -->
    <!-- <div class="header-top bg-light">
        <div class="container">
            <div class="row row-cols-xl-2 align-items-center">
                <div class="col d-none d-lg-block">
                    <div class="header-top-lan-curr-link">
                        <div class="header-top-links">
                            <span>Call Us</span><a href="tel:+10123456789"> +1 (012) 345-6789</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <p class="header-top-message">
                        Order online for pickup or delivery.
                        @if (isset($topFlashDeal))
                            <a href="{{ route('storefront.home') }}#deals">See today's deal</a>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div> -->
@php   $settings = \App\Models\Setting::first(); @endphp
    <!-- Header Bottom -->
    <div class="header-bottom">
        <div class="header-sticky">
            <div class="container">
                <div class="row align-items-center">

                    <div class="col-xl-2 col-6">
                        <div class="header-logo">
                            <a href="{{ route('storefront.home') }}">
                                <img src="{{ Storage::url($settings->logo) ?? asset('storefront/assets/images/logo/logo.png') }}" alt="{{ config('app.name') }}" />
                            </a>
                        </div>
                    </div>

                    <div class="col-xl-8 d-none d-xl-block">
                        <div class="main-menu position-relative">
                            <ul>
                                <li><a href="{{ route('storefront.home') }}"><span>Home</span></a></li>

                                <li class="has-children position-static">
                                    <a href="{{ route('storefront.menu.index') }}"><span>Menu</span> <i class="fa fa-angle-down"></i></a>
                                    <ul class="mega-menu mega-menu-auto">
                                        @forelse ($categories ?? [] as $main)
                                            <li class="col">
                                                <h4 class="mega-menu-title">{{ $main->name }}</h4>
                                                <ul class="mb-n2">
                                                    @forelse ($main->children as $child)
                                                        <li><a href="{{ route('storefront.category', $child->slug) }}">{{ $child->name }}</a></li>
                                                    @empty
                                                        <li><a href="{{ route('storefront.category', $main->slug) }}">{{ $main->name }}</a></li>
                                                    @endforelse
                                                </ul>
                                            </li>
                                        @empty
                                            <li class="col">
                                                <h4 class="mega-menu-title">Full Menu</h4>
                                                <ul class="mb-n2">
                                                    <li><a href="{{ route('storefront.home') }}#menu">Browse Dishes</a></li>
                                                </ul>
                                            </li>
                                        @endforelse
                                    </ul>
                                </li>

                                 <li><a href="{{ route('storefront.deals.index') }}"><span>Deals</span></a></li>
                                <li><a href="{{ route('storefront.track-order') }}"><span>Track Order</span></a></li>
                                <li><a href="{{route('storefront.contact')}}"><span>Contact</span></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-xl-2 col-6">
                        <div class="header-actions">
                            <a href="javascript:void(0)" class="header-action-btn header-action-btn-search"><i class="pe-7s-search"></i></a>

                            @auth('customer')
                                <a href="{{ route('storefront.account') }}" class="header-action-btn d-none d-md-block">
                                    <i class="pe-7s-user"></i>
                                </a>
                            @else
                                <a href="{{ route('storefront.login') }}" class="header-action-btn d-none d-md-block">
                                    <i class="pe-7s-user"></i>
                                </a>
                            @endauth

                            <a href="javascript:void(0)" class="header-action-btn header-action-btn-cart">
                                <i class="pe-7s-shopbag"></i>
                            <span class="header-action-num" style="background-color: var(--mc-gold) !important; color: var(--mc-deep) !important;">{{ $cartCount ?? 0 }}</span>     
                           </a>
                            <a href="javascript:void(0)" class="header-action-btn header-action-btn-menu d-xl-none d-lg-block">
                                <i class="fa fa-bars"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu-wrapper">
        <div class="offcanvas-overlay"></div>
        <div class="mobile-menu-inner">
            <div class="offcanvas-btn-close"><i class="pe-7s-close"></i></div>

           <div class="mobile-navigation">
    <nav>
        <ul class="mobile-menu">
            <li><a href="{{ route('storefront.home') }}">Home</a></li>

            @forelse ($categories ?? [] as $main)
                @if ($main->children->isNotEmpty())
                    <li class="has-children">
                        <a href="#">{{ $main->name }} <i class="fa fa-angle-down"></i></a>
                        <ul class="dropdown">
                            @foreach ($main->children as $child)
                                <li><a href="{{ route('storefront.category', $child->slug) }}">{{ $child->name }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                @else
                    <li><a href="{{ route('storefront.category', $main->slug) }}">{{ $main->name }}</a></li>
                @endif
            @empty
                <li><a href="{{ route('storefront.home') }}#menu">Full Menu</a></li>
            @endforelse

            <li><a href="{{ route('storefront.deals.index') }}"><span>Deals</span></a></li>
            <li><a href="{{ route('storefront.track-order') }}"><span>Track Order</span></a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>

            @auth('customer')
                <li><a href="{{ route('storefront.account') }}">My Account</a></li>
            @else
                <li><a href="{{ route('storefront.login') }}">Login | Register</a></li>
            @endauth
        </ul>
    </nav>
</div>
            <!-- <div class="offcanvas-lag-curr mb-6">
                <h2 class="title">Languages</h2>
                <div class="header-top-lan-curr-link">
                    <div class="header-top-lan dropdown">
                        <button class="dropdown-toggle" data-bs-toggle="dropdown">English <i class="fa fa-angle-down"></i></button>
                        <ul class="dropdown-menu dropdown-menu-right animate slideIndropdown">
                            <li><a class="dropdown-item" href="#">English</a></li>
                            <li><a class="dropdown-item" href="#">Spanish</a></li>
                        </ul>
                    </div>
                    <div class="header-top-curr dropdown">
                        <button class="dropdown-toggle" data-bs-toggle="dropdown">USD <i class="fa fa-angle-down"></i></button>
                        <ul class="dropdown-menu dropdown-menu-right animate slideIndropdown">
                            <li><a class="dropdown-item" href="#">USD</a></li>
                        </ul>
                    </div>
                </div>
            </div> -->

            @php
    $mobileMenuSettings = \App\Models\Setting::current();
@endphp

<div class="mt-auto">
    <ul class="contact-links">
        @if ($mobileMenuSettings->phone)
            <li><i class="fa fa-phone"></i><a href="tel:{{ $mobileMenuSettings->phone }}"> {{ $mobileMenuSettings->phone }}</a></li>
        @endif
        @if ($mobileMenuSettings->email)
            <li><i class="fa fa-envelope-o"></i><a href="mailto:{{ $mobileMenuSettings->email }}"> {{ $mobileMenuSettings->email }}</a></li>
        @endif
        @forelse ($mobileMenuSettings->groupedOpeningHours() as $group)
            <li><i class="fa fa-clock-o"></i> <span>{{ $group['label'] }}, {{ $group['hours'] }}</span></li>
        @empty
            <li><i class="fa fa-clock-o"></i> <span>Hours not set yet.</span></li>
        @endforelse
    </ul>

    @if ($mobileMenuSettings->facebook || $mobileMenuSettings->instagram)
        <div class="widget-social">
            @if ($mobileMenuSettings->facebook)
                <a title="Facebook" href="{{ $mobileMenuSettings->facebook }}" target="_blank" rel="noopener"><i class="fa fa-facebook-f"></i></a>
            @endif
            @if ($mobileMenuSettings->instagram)
                <a title="Instagram" href="{{ $mobileMenuSettings->instagram }}" target="_blank" rel="noopener"><i class="fa fa-instagram"></i></a>
            @endif
        </div>
    @endif
</div>
        </div>
    </div>

    <!-- Search Offcanvas -->
    <div class="offcanvas-search">
        <div class="offcanvas-search-inner">
            <div class="offcanvas-btn-close"><i class="pe-7s-close"></i></div>
            <form class="offcanvas-search-form" action="{{ route('storefront.search') }}" method="GET">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search the menu..." class="offcanvas-search-input">
            </form>
        </div>
    </div>

    <!-- Cart Offcanvas -->
    <div class="cart-offcanvas-wrapper">
        <div class="offcanvas-overlay"></div>
        <div class="cart-offcanvas-inner">
            <div class="offcanvas-btn-close"><i class="pe-7s-close"></i></div>
<div class="offcanvas-cart-content" id="cart-offcanvas-content">
    @include('storefront.partials.cart-offcanvas-content')
</div>
        </div>
    </div>

</div>