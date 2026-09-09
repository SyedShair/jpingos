@if(isset($menuItem) && $menuItem)

    {{-- Deals attached to a menu item --}}
    <button
        type="button"
        class="btn btn-outline-dark btn-hover-primary js-add-to-cart"
        data-menu-item-id="{{ $menuItem->id }}"
        data-deal-id="{{ $deal->id }}"
    >
        <i class="fa fa-shopping-cart me-2"></i>
        Add To Cart
    </button>

@elseif(in_array($deal->type, ['combo','bundle']))

    {{-- Combo / Bundle --}}
    <button
        type="button"
        class="btn btn-outline-dark btn-hover-primary js-add-bundle-to-cart"
        data-deal-id="{{ $deal->id }}"
    >
        <i class="fa fa-shopping-cart me-2"></i>
        Add To Cart
    </button>

@else

    <a href="{{ route('storefront.menu') }}" class="btn btn-outline-dark btn-hover-primary">
        Browse Menu
    </a>

@endif