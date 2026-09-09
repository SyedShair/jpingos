// (function ($) {
//     "use strict";

//     // Get selected option_value IDs
//     function collectSelectedOptions($button) {

//         const containerId = $button.data('options-container');
//         if (!containerId) return [];

//         return $('#' + containerId)
//             .find('input:checked')
//             .map(function () {
//                 return Number($(this).val()); // option_values.id
//             })
//             .get();
//     }

//     // Get quantity
//     function resolveQuantity($button) {

//         const quantityInputId = $button.data('quantity-input');
//         if (!quantityInputId) return 1;

//         const qty = parseInt($('#' + quantityInputId).val(), 10);

//         return Number.isFinite(qty) && qty > 0 ? qty : 1;
//     }

//     // Open cart sidebar
//     function openCartOffcanvas() {
//         $('body').addClass('fix');
//         $('.cart-offcanvas-wrapper').addClass('open');
//     }

//     // Update cart HTML & counter
//     function updateCartUI(data) {
//         $('.header-action-btn-cart .header-action-num').text(data.count);
//         $('#cart-offcanvas-content').html(data.html);
//     }

//     /* ================= ADD TO CART ================= */

//     $(document).on('click', '.js-add-to-cart', function () {

//         const $btn = $(this);

//         if ($btn.prop('disabled')) return;

//         const menuItemId = $btn.data('menu-item-id');

//         if (!menuItemId) {
//             alert('Dish not found. Please refresh.');
//             return;
//         }

//         const quantity = resolveQuantity($btn);
//         const options = collectSelectedOptions($btn);

//         const original = $btn.html();

//        $btn
//     .prop('disabled', true)
//     .addClass('disabled')
//     .html(`
//         <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
//         Adding...
//     `);

//         fetch('/cart/add', {

//             method: 'POST',

//             headers: {
//                 'Content-Type': 'application/json',
//                 'Accept': 'application/json',
//                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//             },

//             body: JSON.stringify({
//                 menu_item_id: menuItemId,
//                 quantity: quantity,
//                  deal_id: $btn.data('deal-id'),
//                 options: options
//             })

//         })
//         .then(res => {
//             if (!res.ok) {
//                 return res.json().then(err => {
//                     throw new Error(err.message || 'Add to cart failed.');
//                 });
//             }
//             return res.json();
//         })
//         .then(data => {
//             updateCartUI(data);
//             openCartOffcanvas();
//         })
//         .catch(err => {
//             alert(err.message);
//         })
//         .finally(() => {
//             $btn.prop('disabled', false).removeClass('disabled').html(original);
//         });

//     });


    
//     /* ================= REMOVE CART ITEM ================= */

//     $(document).on('click', '.js-cart-remove', function () {

//         const rowId = $(this).data('row-id');

//         fetch('/cart/' + rowId, {

//             method: 'DELETE',

//             headers: {
//                 'Accept': 'application/json',
//                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//             }

//         })
//         .then(res => res.json())
//         .then(updateCartUI);

//     });

// })(jQuery);