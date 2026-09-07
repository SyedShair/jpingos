@push('styles')
<style>
    .mega-menu-auto {
        display: flex;
        flex-wrap: nowrap;
        gap: 40px;
        width: auto;
        min-width: 100%;
        padding: 30px 40px;
    }

    .mega-menu-auto > .col {
        flex: 0 0 auto;
        width: auto;
        white-space: nowrap;
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
    }

    .mega-menu-auto ul.mb-n2 li a:hover {
        color: var(--bs-primary, #2fb35b);
    }
</style>
@endpush

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

    <!-- Header Bottom -->
    <div class="header-bottom">
        <div class="header-sticky">
            <div class="container">
                <div class="row align-items-center">

                    <div class="col-xl-2 col-6">
                        <div class="header-logo">
                            <a href="{{ route('storefront.home') }}">
                                <img src="{{ asset('storefront/assets/images/logo/logo.png') }}" alt="{{ config('app.name') }}" />
                            </a>
                        </div>
                    </div>

                    <div class="col-xl-8 d-none d-xl-block">
                        <div class="main-menu position-relative">
                            <ul>
                                <li><a href="{{ route('storefront.home') }}"><span>Home</span></a></li>

                                <li class="has-children position-static">
                                    <a href="#"><span>Menu</span> <i class="fa fa-angle-down"></i></a>
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
                                <li><a href="#"><span>About</span></a></li>
                                <li><a href="#"><span>Contact</span></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-xl-2 col-6">
                        <div class="header-actions">
                            <a href="javascript:void(0)" class="header-action-btn header-action-btn-search"><i class="pe-7s-search"></i></a>

                           

                            <a href="javascript:void(0)" class="header-action-btn header-action-btn-cart">
                                <i class="pe-7s-shopbag"></i>
                            <span class="header-action-num">{{ $cartCount ?? 0 }}</span>     
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
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
    </nav>
</div>
            <div class="offcanvas-lag-curr mb-6">
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
            </div>

            <div class="mt-auto">
                <ul class="contact-links">
                    <li><i class="fa fa-phone"></i><a href="tel:+10123456789"> +1 (012) 345-6789</a></li>
                    <li><i class="fa fa-envelope-o"></i><a href="mailto:hello@example.com"> hello@example.com</a></li>
                    <li><i class="fa fa-clock-o"></i> <span>Mon – Sun, 9:00 – 22:00</span></li>
                </ul>

                <div class="widget-social">
                    <a title="Facebook" href="#"><i class="fa fa-facebook-f"></i></a>
                    <a title="Twitter" href="#"><i class="fa fa-twitter"></i></a>
                    <a title="Instagram" href="#"><i class="fa fa-instagram"></i></a>
                </div>
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