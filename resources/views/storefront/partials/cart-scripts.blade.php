<script>
(function ($) {
    "use strict";

    /* ==========================================
       GET SELECTED OPTION VALUES
    ========================================== */
    function collectSelectedOptions($button) {
        const containerId = $button.data("options-container");
        if (!containerId) return [];

        return $("#" + containerId)
            .find("input:checked")
            .map(function () {
                return Number($(this).val());
            })
            .get();
    }

    /* ==========================================
       GET QUANTITY
    ========================================== */
    function resolveQuantity($button) {
        const quantityInputId = $button.data("quantity-input");
        if (!quantityInputId) return 1;

        const qty = parseInt($("#" + quantityInputId).val(), 10);

        return Number.isFinite(qty) && qty > 0 ? qty : 1;
    }

    /* ==========================================
       OPEN CART OFFCANVAS
    ========================================== */
    function openCartOffcanvas() {
        $("body").addClass("fix");
        $(".cart-offcanvas-wrapper").addClass("open");
    }

    /* ==========================================
       UPDATE CART HTML + COUNT
    ========================================== */
    function updateCartUI(data) {
        $(".header-action-btn-cart .header-action-num").text(data.count);
        $("#cart-offcanvas-content").html(data.html);
    }

    function csrfToken() {
        return $('meta[name="csrf-token"]').attr("content");
    }

    /* ==========================================
       SHARED ADD TO CART REQUEST
    ========================================== */
    function submitAddToCart($btn, url, payload) {

        if ($btn.prop("disabled")) return;

        const original = $btn.html();

        $btn
            .prop("disabled", true)
            .addClass("disabled")
            .html(`
                <span class="spinner-border spinner-border-sm me-2"
                      role="status"
                      aria-hidden="true"></span>
                Adding...
            `);

        fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrfToken()
            },
            body: JSON.stringify(payload)
        })
        .then(async (response) => {

            const json = await response.json();

            if (!response.ok) {
                throw new Error(json.message || "Add to cart failed.");
            }

            return json;
        })
        .then((data) => {
            updateCartUI(data);
            openCartOffcanvas();
        })
        .catch((error) => {
            alert(error.message);
        })
        .finally(() => {
            $btn
                .prop("disabled", false)
                .removeClass("disabled")
                .html(original);
        });
    }


    $(document).on("click", ".header-action-btn-cart", function () {
        openCartOffcanvas();
    });
    /* ==========================================
       ADD NORMAL ITEM / DEAL ITEM
       Flash Deal, Happy Hour, Lunch Special,
       BOGO, Free Gift
    ========================================== */
    $(document).on("click", ".js-add-to-cart", function () {

        const $btn = $(this);

        const menuItemId = Number($btn.data("menu-item-id"));
        const dealId = $btn.data("deal-id") || null;

        if (!menuItemId) {
            alert("Dish not found. Please refresh.");
            return;
        }

        submitAddToCart($btn, "/cart/add", {
            menu_item_id: menuItemId,
            deal_id: dealId,
            quantity: resolveQuantity($btn),
            options: collectSelectedOptions($btn)
        });
    });

    /* ==========================================
       ADD COMBO / BUNDLE DEAL
    ========================================== */
    $(document).on("click", ".js-add-bundle-to-cart", function () {

        const $btn = $(this);

        const dealId = Number($btn.data("deal-id"));

        if (!dealId) {
            alert("Deal not found. Please refresh.");
            return;
        }

        submitAddToCart($btn, "/cart/bundle", {
            deal_id: dealId,
            quantity: resolveQuantity($btn)
        });
    });

    /* ==========================================
       REMOVE CART ITEM
    ========================================== */
    $(document).on("click", ".js-cart-remove", function () {

        const rowId = $(this).data("row-id");

        fetch("/cart/" + rowId, {
            method: "DELETE",
            headers: {
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrfToken()
            }
        })
        .then((response) => response.json())
        .then((data) => {
            updateCartUI(data);
        });
    });

})(jQuery);
</script>