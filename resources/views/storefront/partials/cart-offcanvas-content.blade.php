<h2 class="offcanvas-cart-title mb-10">Your Order</h2>

@forelse ($cartItems ?? [] as $cartItem)
    <div class="cart-product-wrapper mb-6">
        <div class="single-cart-product">
            <div class="cart-product-thumb">
                <a href="{{ route('storefront.dish', $cartItem->menuItem->slug) }}">
                    <img src="{{ $cartItem->menuItem->image_url }}" alt="{{ $cartItem->menuItem->name }}">
                </a>
            </div>
            <div class="cart-product-content">
                <h3 class="title">
                    <a href="{{ route('storefront.dish', $cartItem->menuItem->slug) }}">{{ $cartItem->menuItem->name }}</a>
                </h3>
                @if ($cartItem->options->isNotEmpty())
                    <p class="mb-1 small text-muted">
                        {{ $cartItem->options->pluck('name')->implode(', ') }}
                    </p>
                @endif
                <span class="price">
                    <span class="new">£{{ number_format($cartItem->line_total, 2) }}</span>
                </span>
                <span class="d-block small text-muted">Qty: {{ $cartItem->quantity }}</span>
            </div>
        </div>

        <div class="cart-product-remove">
            <a href="javascript:void(0)" class="js-cart-remove" data-row-id="{{ $cartItem->row_id }}"><i class="fa fa-trash"></i></a>
        </div>
    </div>
@empty
    <p class="text-muted">Your order is empty. Browse the menu and add a dish to get started.</p>
@endforelse

@if (($cartItems ?? collect())->isNotEmpty())
    <div class="cart-product-total">
        <span class="value">Subtotal</span>
        <span class="price">£{{ number_format($cartSubtotal ?? 0, 2) }}</span>
    </div>

    <div class="cart-product-btn mt-4">
        <a href="{{ route('storefront.cart') }}" class="btn btn-dark btn-hover-primary rounded-0 w-100">View Order</a>
        <a href="{{ route('storefront.checkout') }}" class="btn btn-dark btn-hover-primary rounded-0 w-100 mt-4">Checkout</a>
    </div>
@endif