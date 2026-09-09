{{--
  Usage: @include('storefront.partials.add-to-cart-button', ['item' => $dish])
  Optional: 'optionsContainer' => 'dish-customizations-list' (reads checked
  inputs from that container as the selected option_value ids), and
  'quantityInput' => 'dish-quantity' (reads a quantity <input id="...">).
  Optional: 'deal' => $deal — the specific deal object whose price is
  currently displayed for this item (e.g. on a Daily Deals card). Pins
  the cart to that exact deal's price instead of letting the server
  guess which active deal applies, in case more than one does.
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
    @if(!empty($deal))
        data-deal-id="{{ $deal->id }}"
    @endif
>
    <i class="fa fa-shopping-cart me-2"></i>
    Add To Cart
</button>