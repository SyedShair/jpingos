<!-- Modal Start -->
<div class="modalquickview modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content position-relative">

            <button class="btn close" data-bs-dismiss="modal">×</button>

            <!-- Quickview Loading Overlay -->
            <div class="quickview-loader-overlay hidden" id="quickview-loader">
                <div id="ajaxLoaderHorizontal">
                    <div class="ajaxInner"></div>
                    <div class="ajaxInner"></div>
                    <div class="ajaxInner"></div>
                </div>
            </div>

            <div class="row" id="quickview-body">

                <!-- LEFT SIDE -->
                <div class="col-md-6 col-12">

                    <div class="modal-product-carousel">
                        <div class="swiper-container">

                            <div class="swiper-wrapper" id="quickview-images">
                                <!-- Images Loaded by JS -->
                            </div>

                            <div class="swiper-product-button-next swiper-button-next">
                                <i class="pe-7s-angle-right"></i>
                            </div>

                            <div class="swiper-product-button-prev swiper-button-prev">
                                <i class="pe-7s-angle-left"></i>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- RIGHT SIDE -->
                <div class="col-md-6 col-12 overflow-hidden position-relative">

                    <div class="product-summery">

                        <div class="product-head mb-3">
                            <h2 class="product-title" id="quickview-name"></h2>
                        </div>

                        <div class="price-box mb-2" id="quickview-price-box">
                            <span class="regular-price" id="quickview-live-price"></span>
                            <span class="old-price" id="quickview-old-price"></span>
                        </div>

                        <!-- Deal Countdown -->
                        <div class="countdown-area mb-3" id="quickview-countdown-wrap" style="display:none;">
                            <p class="inner-desc mb-1">Hurry Up! Offer Ends In:</p>
                            <div class="countdown-wrapper d-flex" id="quickview-countdown"></div>
                        </div>

                        <!-- Rating -->
                        <span class="ratings justify-content-start">
                            <span class="rating-wrap">
                                <span class="star" style="width:100%"></span>
                            </span>
                            <span class="rating-num">(5)</span>
                        </span>

                        <!-- SKU -->
                        <div class="sku mb-3">
                            <span id="quickview-sku">SKU: —</span>
                        </div>

                        <!-- Description -->
                        <p class="desc-content mb-4" id="quickview-description"></p>

                        <!-- Spice -->
                        <div class="product-meta mb-3" id="quickview-spice" style="display:none;">
                            <span>Spice Level :</span>
                            <strong id="quickview-spice-value"></strong>
                        </div>

                        <!-- Dietary -->
                        <div class="product-meta mb-4" id="quickview-dietary" style="display:none;">
                            <span>Dietary :</span>
                            <span id="quickview-dietary-value"></span>
                        </div>

                        <!-- Customizations Start -->
                        <div id="quickview-customizations" class="product-meta mb-5 d-none">
                            <span class="d-block mb-2">Customize Your Order :</span>

                            <div id="quickview-customizations-list">
                                <!-- Loaded by JS -->
                            </div>
                        </div>
                        <!-- Customizations End -->

                       


                        <!-- Buttons -->
                        <div class="cart-wishlist-btn pb-4 mb-n3">

                            <div class="add-to_cart mb-3">
                                <a class="btn btn-outline-dark btn-hover-primary"
                                   id="quickview-view-link"
                                   href="">
                                   Buy Now
                                </a>
                            </div>

                           

                        </div>

                        <!-- Share -->
                        <div class="social-share">
                            <span>Share :</span>

                            <a href="#"><i class="fa fa-facebook-square facebook-color"></i></a>
                            <a href="#"><i class="fa fa-twitter-square twitter-color"></i></a>
                            <a href="#"><i class="fa fa-linkedin-square linkedin-color"></i></a>
                            <a href="#"><i class="fa fa-pinterest-square pinterest-color"></i></a>
                        </div>

                        <!-- Delivery -->
                        <ul class="product-delivery-policy border-top pt-4 mt-4 border-bottom pb-4">
                            <li><i class="fa fa-check-square"></i> Freshly prepared to order</li>
                            <li><i class="fa fa-truck"></i> Dine-in, pickup, or delivery</li>
                            <li><i class="fa fa-refresh"></i> Ask us about allergens on request</li>
                        </ul>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
<!-- Modal End -->