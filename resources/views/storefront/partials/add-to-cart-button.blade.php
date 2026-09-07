{{--
  Usage: @include('storefront.partials.add-to-cart-button', ['item' => $dish])
  Optional: 'optionsContainer' => 'dish-customizations-list' (reads checked
  inputs from that container as the selected option_value ids), and
  'quantityInput' => 'dish-quantity' (reads a quantity <input id="...">).
--}}
<button
    type="button"
    class="btn  btn-outline-dark btn-hover-primary js-add-to-cart"
    data-menu-item-id="{{ $item->id }}"
    @if(!empty($optionsContainer))
        data-options-container="{{ $optionsContainer }}"
    @endif
    @if(!empty($quantityInput))
        data-quantity-input="{{ $quantityInput }}"
    @endif
>
    <i class="fa fa-shopping-cart me-2"></i>
    Add To Cart
</button>
