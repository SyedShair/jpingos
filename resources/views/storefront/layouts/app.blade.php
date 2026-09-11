<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Restaurant'))</title>
    <link rel="shortcut icon" href="{{ asset('storefront/assets/images/favicon.ico') }}">

    <link rel="stylesheet" href="{{ asset('storefront/assets/css/vendor.min.css') }}">
    <link rel="stylesheet" href="{{ asset('storefront/assets/css/plugins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('storefront/assets/css/style.min.css') }}?v={{ filemtime(public_path('storefront/assets/css/style.min.css')) }}">
<style>

    .header-actions .header-action-btn .header-action-num {

        background: #D12026 !important;
    }
    .btn-primary {
        border-color: #D12026;
        background: #D12026 !important;
        color: #fff;
    }
    .main-menu>ul>li>a>span:before{
        background-color:#D12026 ;
        color: #D12026;
    }
    .main-menu>ul>li>a:hover{
        color:#D12026;
    }
    .mega-menu>li .mega-menu-title::after{
        background-color:#D12026!important;

    }
    .mega-menu{
        border-bottom:3px solid #D12026;
    }
a{
    color: #D12026;
}
li>a:hover{
    color:#D12026!important;
}
    .product-tab-nav .nav-item .nav-link:hover, .product-tab-nav .nav-item .nav-link.active {
        color: #D12026 !important;
    }
    .product-tab-nav .nav-item .nav-link:hover, .product-tab-nav .nav-item .nav-link.active {
        color: #D12026 !important;
    }
    a:hover {
        color: #D12026 !important;
    }
    .product-tab-nav .nav-item .nav-link:after {
        position: absolute;
        content: "";
        color: #D12026;
        background: #D12026;
        width: 0%;
        height: 2px;
        -webkit-transition: all 0.3s ease 0s;
        -o-transition: all 0.3s ease 0s;
        transition: all 0.3s ease 0s;
        top: 100%;
        left: 0;
    }
    .new{
        color: #D12026 !important;
    }
    .product .thumb .badges span.sale {
        background-color: #D12026;
    }
    .btn-hover-primary:hover{
   background: #D12026;
        color: #FFFFFF !important;
 }
    .actions a:hover,a>i:hover{
        background: #D12026 !important;
        color: #FFFFFF !important;
    }
    .sub-title span{
        color: #D12026 !important;
    }

    element.style {
    }
    .product-list-title span {
        width: 100px;
        height: 2px;
        display: block;
        border: 1px solid #D12026;
        position: absolute;
        bottom: -2px;
        left: 0;
    }
    .product-list-carousel .swiper-button-prev i:hover, .product-list-carousel .swiper-button-next i:hover, .product-list-carousel-2 .swiper-button-prev i:hover, .product-list-carousel-2 .swiper-button-next i:hover, .product-list-carousel-3 .swiper-button-prev i:hover, .product-list-carousel-3 .swiper-button-next i:hover {
        color: #fff;
        background: #D12026 !important;
        -webkit-transition: .3s;
        -o-transition: .3s;
        transition: .3s;
    }
    .banner:hover .small-banner-content .btn {
        background: #D12026;
    }
    .offcanvas-search-inner .offcanvas-btn-close:hover{
        font-size: 50px;
        color: #D12026;
        opacity: 1;
    }
    .shop_toolbar_btn{
        color: #D12026 !important;
    }
    .shop_toolbar_wrapper .shop_toolbar_btn button.active{
        border: 1px solid #D12026;
        color: #D12026;
    }
    .shop_toolbar_wrapper .shop_toolbar_btn button:hover {
        color: #D12026;
        border-color: #D12026;
    }
    .shop_toolbar_wrapper .shop-top-bar-right .shop-short-by .nice-select:focus {
        border-color: #D12026;
    }
    .shop_toolbar_wrapper .shop-top-bar-right .shop-short-by .nice-select li:hover {
        padding-left: 5px;
        display: block;
        color: #D12026;
    }
    .pagination .page-item .page-link.active {
        background: #D12026;
        color: #fff;
        border-color: #D12026;
    }
    .page-link {
        position: relative;
        display: block;
        color: #D12026;
        text-decoration: none;
        background-color: #fff;
        border: 1px solid #D12026;
        -webkit-transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
        -o-transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
    }
    .pagination .page-item .page-link:hover {
        background: #D12026;
        color: #fff !important;
        border-color: #D12026;
    }
    .product-summery .price-box .regular-price {
        font-size: 18px;
        font-weight: 600;
        margin-right: 5px;
        color: #D12026;
    }
    .quantity .cart-plus-minus>.qtybutton:hover {
        -webkit-transition: .3s;
        -o-transition: .3s;
        transition: .3s;
        background-color: #D12026;
        color: #fff;
    }
    .product-details-img .single-product-thumb .swiper-slide-thumb-active img {
        cursor: pointer;
        border-color: #D12026;
        -webkit-transition: .3s;
        -o-transition: .3s;
        transition: .3s;
    }
    .product-details-img .single-product-thumb .swiper-button-prev i:hover, .product-details-img .single-product-thumb .swiper-button-next i:hover {
        color: #fff;
        background: #D12026 !important;
        -webkit-transition: .3s;
        -o-transition: .3s;
        transition: .3s;
    }
    .single-product-tab .nav-tabs .nav-item .nav-link.active {
        color: #D12026;
    }
    .myaccount-tab-menu a:hover, .myaccount-tab-menu a.active {
        background-color: #D12026;
        border-color: #D12026;
        color: #fff !important;
    }
    .myaccount-content .welcome strong {
        font-weight: 600;
        color: #D12026;
    }
    .checkbox-form .title::before {
        content: "";
        width: 100px;
        height: 2px;
        background: #D12026;
        position: absolute;
        top: 100%;
        left: 0;
    }
    .your-order-area .title::before {
        content: "";
        background: #D12026;
        width: 100px;
        height: 2px;
        left: 0;
        top: 100%;
        position: absolute;
    }
    .payment-accordion .single-payment .panel-title .collapse-off:hover {
        background: #D12026;
        color: #fff !important;
    }
    .section-title span {
        content: "";
        position: absolute;
        background: #D12026;
        width: 100px;
        height: 2px;
    }
    .offcanvas-btn-close > i{
        color: #D12026 !important;
    }
    .pe-7s-angle-left{
        color: #D12026 !important;
    }
    .pe-7s-angle-left:hover{
        color: #fff !important;
        background: #D12026 !important;
    }
    .product-carousel .swiper-button-prev i:hover, .product-carousel .swiper-button-next i:hover, .product-deal-carousel .swiper-button-prev i:hover, .product-deal-carousel .swiper-button-next i:hover {
        color: #fff;
        background: #D12026 !important;
        -webkit-transition: .3s;
        -o-transition: .3s;
        transition: .3s;
    }
    a:hover {
        color: #D12026 !important;
    }
    .scroll-top:hover{
        background: #D12026;
    }
    .pe-7s-angle-right{
        color: #D12026 !important;
    }
    .pe-7s-angle-right:hover{
        color: #fff !important;
        background: #D12026 !important;
    }
    .pe-7s-angle-left:hover{
        color: #fff !important;
    }
    .btn-primary {
        border-color: #D12026;
        background-color: #D12026;
        color: #fff;
    } .btn-primary:hover {
        border-color: #D12026;
        background-color: #D12026;
        color: #fff;
    }
    .social-button {
        background-position: 25px 0px;
        box-sizing: border-box;
        color: rgb(255, 255, 255);
        cursor: pointer;
        display: inline-block;
        height: 50px;
        line-height: 50px;
        text-align: left;
        text-decoration: none;
        text-transform: uppercase;
        vertical-align: middle;
        width: 100%;
        border-radius: 3px;
        margin: 10px auto;
        outline: rgb(255, 255, 255) none 0px;
        padding-left: 20%;
        transition: all 0.2s cubic-bezier(0.72, 0.01, 0.56, 1) 0s;
        -webkit-transition: all .3s ease;
        -moz-transition: all .3s ease;
        -ms-transition: all .3s ease;
        -o-transition: all .3s ease;
        transition: all .3s ease;
    }

    #facebook-connect {
        background: rgb(255, 255, 255) url('https://raw.githubusercontent.com/eswarasai/social-login/master/img/facebook.svg?sanitize=true') no-repeat scroll 5px 0px / 30px 50px padding-box border-box;
        border: 1px solid rgb(60, 90, 154);
    }

    #facebook-connect:hover {
        border-color: rgb(60, 90, 154);
        background: rgb(60, 90, 154) url('https://raw.githubusercontent.com/eswarasai/social-login/master/img/facebook-white.svg?sanitize=true') no-repeat scroll 5px 0px / 30px 50px padding-box border-box;
        -webkit-transition: all .8s ease-out;
        -moz-transition: all .3s ease;
        -ms-transition: all .3s ease;
        -o-transition: all .3s ease;
        transition: all .3s ease-out;
    }

    #facebook-connect span {
        box-sizing: border-box;
        color: rgb(60, 90, 154);
        cursor: pointer;
        text-align: center;
        text-transform: uppercase;
        border: 0px none rgb(255, 255, 255);
        outline: rgb(255, 255, 255) none 0px;
        -webkit-transition: all .3s ease;
        -moz-transition: all .3s ease;
        -ms-transition: all .3s ease;
        -o-transition: all .3s ease;
        transition: all .3s ease;
    }

    #facebook-connect:hover span {
        color: #FFF;
        -webkit-transition: all .3s ease;
        -moz-transition: all .3s ease;
        -ms-transition: all .3s ease;
        -o-transition: all .3s ease;
        transition: all .3s ease;
    }

    #google-connect {
        background: rgb(255, 255, 255) url('https://raw.githubusercontent.com/eswarasai/social-login/master/img/google-plus.png') no-repeat scroll 5px 0px / 50px 50px padding-box border-box;
        border: 1px solid rgb(220, 74, 61);
    }

    #google-connect:hover {
        border-color: rgb(220, 74, 61);
        background: rgb(220, 74, 61) url('https://raw.githubusercontent.com/eswarasai/social-login/master/img/google-plus-white.png') no-repeat scroll 5px 0px / 50px 50px padding-box border-box;
        -webkit-transition: all .8s ease-out;
        -moz-transition: all .3s ease;
        -ms-transition: all .3s ease;
        -o-transition: all .3s ease;
        transition: all .3s ease-out;
    }

    #google-connect span {
        box-sizing: border-box;
        color: rgb(220, 74, 61);
        cursor: pointer;
        text-align: center;
        text-transform: uppercase;
        border: 0px none rgb(220, 74, 61);
        outline: rgb(255, 255, 255) none 0px;
        -webkit-transition: all .3s ease;
        -moz-transition: all .3s ease;
        -ms-transition: all .3s ease;
        -o-transition: all .3s ease;
        transition: all .3s ease;
    }

    #google-connect:hover span {
        color: #FFF;
        -webkit-transition: all .3s ease;
        -moz-transition: all .3s ease;
        -ms-transition: all .3s ease;
        -o-transition: all .3s ease;
        transition: all .3s ease;
    }

    #twitter-connect {
        background: rgb(255, 255, 255) url('https://raw.githubusercontent.com/eswarasai/social-login/master/img/twitter.png') no-repeat scroll 5px 1px / 45px 45px padding-box border-box;
        border: 1px solid rgb(85, 172, 238);
    }

    #twitter-connect:hover {
        border-color: rgb(85, 172, 238);
        background: rgb(85, 172, 238) url('https://raw.githubusercontent.com/eswarasai/social-login/master/img/twitter-white.png') no-repeat scroll 5px 1px / 45px 45px padding-box border-box;
        -webkit-transition: all .8s ease-out;
        -moz-transition: all .3s ease;
        -ms-transition: all .3s ease;
        -o-transition: all .3s ease;
        transition: all .3s ease-out;
    }

    #twitter-connect span {
        box-sizing: border-box;
        color: rgb(85, 172, 238);
        cursor: pointer;
        text-align: center;
        text-transform: uppercase;
        border: 0px none rgb(220, 74, 61);
        outline: rgb(255, 255, 255) none 0px;
        -webkit-transition: all .3s ease;
        -moz-transition: all .3s ease;
        -ms-transition: all .3s ease;
        -o-transition: all .3s ease;
        transition: all .3s ease;
    }

    #twitter-connect:hover span {
        color: #FFF;
        -webkit-transition: all .3s ease;
        -moz-transition: all .3s ease;
        -ms-transition: all .3s ease;
        -o-transition: all .3s ease;
        transition: all .3s ease;
    }

    #linkedin-connect {
        background: rgb(255, 255, 255) url('https://raw.githubusercontent.com/eswarasai/social-login/master/img/linkedin.svg?sanitize=true') no-repeat scroll 13px 0px / 28px 45px padding-box border-box;
        border: 1px solid rgb(0, 119, 181);
    }

    #linkedin-connect:hover {
        border-color: rgb(0, 119, 181);
        background: rgb(0, 119, 181) url('https://raw.githubusercontent.com/eswarasai/social-login/master/img/linkedin-white.svg?sanitize=true') no-repeat scroll 13px 0px / 28px 45px padding-box border-box;
        -webkit-transition: all .8s ease-out;
        -moz-transition: all .3s ease;
        -ms-transition: all .3s ease;
        -o-transition: all .3s ease;
        transition: all .3s ease-out;
    }

    #linkedin-connect span {
        box-sizing: border-box;
        color: rgb(0, 119, 181);
        cursor: pointer;
        text-align: center;
        text-transform: uppercase;
        border: 0px none rgb(0, 119, 181);
        outline: rgb(255, 255, 255) none 0px;
        -webkit-transition: all .3s ease;
        -moz-transition: all .3s ease;
        -ms-transition: all .3s ease;
        -o-transition: all .3s ease;
        transition: all .3s ease;
    }

    #linkedin-connect:hover span {
        color: #FFF;
        -webkit-transition: all .3s ease;
        -moz-transition: all .3s ease;
        -ms-transition: all .3s ease;
        -o-transition: all .3s ease;
        transition: all .3s ease;
    }
.custom{
    width: 100%;
    background: #D12026;
    color: #FFFFFF;
    padding-top: 16px;
    padding-bottom: 16px;
}
    .actions a:hover, a>i:hover {
        background: #fff !important;
        color: #D12026 !important;
    }
    .btn-primary {
        border-color: #D12026;
        background: #D12026 !important;
        color: #fff !important;

    }

    .btn-primary:hover {
        border-color: #D12026;
        background: #D12026 !important;
        color: #fff !important;

    }
    .btn-dark {
        border-color: #212121;
        background-color: #212121;
        color: #fff;
        color: #fff !important;
    }
    a {
         color: #000000 !important;
        text-decoration: none;
    }
    .btn-c{
        border-color: #000000;
        background: #000000 !important;
        color: #fff !important;
    }
    .product .thumb .actions .action:hover:not(.active) {
        color: #fff;
         background-color: #D12026 !important;
    }
    .action i:hover{
      background: #D12026 !important;
        color: #FFFFFF !important;
    }

    .pagination .page-item .page-link.active {
        background: #D12026;
        color: #fff;
        color: #fff !important;
        border-color: #D12026;
    }
    @-webkit-keyframes blinker {
        from {opacity: 1.0;}
        to {opacity: 0.0;}
    }
    .blink{
        text-decoration: blink;
        -webkit-animation-name: blinker;
        -webkit-animation-duration: 0.6s;
        -webkit-animation-iteration-count:infinite;
        -webkit-animation-timing-function:ease-in-out;
        -webkit-animation-direction: alternate;
    }
    .product .thumb .actions .action:hover{
        color: #fff !important;
    }
    .single-product-list {
        display: -webkit-box;
        display: -webkit-flex;
        text-align: justify;
        display: -ms-flexbox;
        margin: 14px;
    }
    .intl-tel-input {
        display: table-cell;
    }
    .intl-tel-input .selected-flag {
        z-index: 4;
    }
    .intl-tel-input .country-list {
        z-index: 5;
    }
    .input-group .intl-tel-input .form-control {
        border-top-left-radius: 4px;
        border-top-right-radius: 0;
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 0;
    }
    .intl-tel-input.allow-dropdown input, .intl-tel-input.allow-dropdown input[type=text], .intl-tel-input.allow-dropdown input[type=tel] {
        padding-right: 6px;
        padding-left: 52px;
        padding-top: 22px;
        padding-bottom: 22px;
        margin-left: 0;
    }

    .alert {
        position: relative;
        padding: 8px 1rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
        border-radius: 0.25rem;
    }
    @media (min-width: 1600px)
    {  .container, .container-sm, .container-md, .container-lg, .container-xl, .container-xxl {
            max-width: 1500px;
        }
    }
    @keyframes ajaxLoaderHorizontal
        /*
          I have the same but in vertical version
          if you want.
        */
    {
        0%, 40%, 100%
        {
            background-color: #111;
            box-shadow: 0 5px 6px rgba(10, 10, 10, 0.1);
            transform: scaleY(0.7);
        }

        20%
        {
            background-color: #FEFEFE;
            box-shadow: 0 5px 6px rgba(10, 10, 10, 0.9);
            transform: scaleY(1);
        }
    }

    div#ajaxLoaderHorizontal
    {
        width: 50px;
        text-align: center;
        margin: 10px auto;
    }

    div#ajaxLoaderHorizontal div.ajaxInner
    {
        height: 50px;
        width: 10px;
        display: inline-block;
    }

    div#ajaxLoaderHorizontal div.ajaxInner:nth-child(1)
    {
        animation: ajaxLoaderHorizontal 1.2s ease-in-out infinite;
    }

    div#ajaxLoaderHorizontal div.ajaxInner:nth-child(2)
    {
        animation: ajaxLoaderHorizontal 1.2s ease-in-out -1s infinite;
    }

    div#ajaxLoaderHorizontal div.ajaxInner:nth-child(3)
    {
        animation: ajaxLoaderHorizontal 1.2s ease-in-out -0.8s infinite;
    }

    div#ajaxContainer
    {
        height: 30px;
        width: 90px;
        text-align: center;
        margin: 20px auto 10px auto;
    }

    div#ajaxContainer div.ajaxCircle
    {
        height: 20px;
        width: 20px;
        margin: 5px;
        float: left;
        border-radius: 50%;
        background-color: #111;
        box-shadow: 0px 2px 5px rgba(10, 10, 10, 0.4);
    }

    div#ajaxContainer div.ajaxCircle div.ajaxInnerRing
    {
        background: transparent;
        height: 40%;
        width: 40%;
        border: 1px solid #FEFEFE;
        border-radius: 50%;
        margin: 25%;
        box-shadow: 0 5px 6px rgba(10, 10, 10, 0.4);
        opacity: 0;
    }

    div#ajaxContainer div.ajaxCircle:nth-child(1) div.ajaxInnerRing
    {
        animation: ajaxLoader 1s ease-in-out infinite;
    }

    div#ajaxContainer div.ajaxCircle:nth-child(2) div.ajaxInnerRing
    {
        animation: ajaxLoader 1s ease-in-out -0.8s infinite;
    }

    div#ajaxContainer div.ajaxCircle:nth-child(3) div.ajaxInnerRing
    {
        animation: ajaxLoader 1s ease-in-out -0.6s infinite;
    }

    @keyframes ajaxLoader
    {
        0%, 100%
        {
            opacity: 0;
        }

        50%
        {
            opacity: 1;
        }
    }

    div#circledAjax
    {
        width: 50px;
        height: 50px;
        border: 1px solid #111;
        border-radius: 25px;
        border-bottom-color: transparent;
        border-top-color: transparent;
        text-align: center;
        margin: auto;
    }

    div#circledAjax div.circledAjaxInner
    {
        border-radius: 50%;
        width: 20px;
        height: 20px;
        position: relative;
        background-color: #111;
        box-shadow: 0px 2px 5px rgba(10, 10, 10, 0.4);
        animation: circledAjax 2s ease-in-out infinite;
    }

    div#circledAjax div.circledAjaxInner div.ajaxInnerRing
    {
        background: transparent;
        height: 40%;
        width: 40%;
        border: 1px solid #FEFEFE;
        border-radius: 50%;
        position: relative;
        top: 25%;
        left: 25%;
        box-shadow: 0 5px 6px rgba(10, 10, 10, 0.4);
        animation: ajaxLoader 1s ease-in-out infinite;
    }

    @keyframes circledAjax
    {
        0%, 100%
        {
            top: 15px;
            left: 0px;
        }

        25%, 75%
        {
            left: 15px;
        }

        50%
        {
            left: 30px;
        }
    }

    div#circlesLoader
    {
        width: 50px;
        height: 50px;
        margin: auto;
    }

    div#circlesLoader div#firstCircle
    {
        width: 100%;
        height: 100%;
        border: 4px solid #000;
        border-radius: 50%;
        border-top-color: transparent;
        border-bottom-color: transparent;
        position: relative;
    }

    div#circlesLoader div#secondCircle
    {
        height: 80%;
        width: 80%;
        border: 3px solid #333;
        border-radius: 50%;
        border-top-color: transparent;
        border-right-color: transparent;
        position: relative;
        top: 2px;
        left: 2px;
    }

    div#circlesLoader div#thirdCircle
    {
        height: 80%;
        width: 80%;
        border: 3px solid #F00;
        border-radius: 50%;
        border-bottom-color: transparent;
        border-left-color: transparent;
        position: relative;
        top: 1px;
        left: 1px;
    }

    div#circlesLoader div#firstCircle, div#circlesLoader div#secondCircle, div#circlesLoader div#thirdCircle
    {
        animation: spin 0.8s linear infinite;
    }

    div#circlesLoader div.circle
    {
        position: relative;
        height: 20px;
        width: 20px;
        border-radius: 50%;
        border: none;
        background: #111;
        top: 39px;
        left: 19px;
    }

    div#circlesLoader div.circle div.innerRing
    {
        border: 1px solid #F00;
        height: 50%;
        width: 50%;
        border-radius: 50%;
        position: relative;
        top: 4px;
        left: 4px;
        animation: ajaxLoader 1s ease-in-out infinite;
    }

    @keyframes spin
    {
        0%
        {
            transform: rotate(0deg);
        }

        100%
        {
            transform: rotate(360deg);
        }
    }


     .email-box_error {
        border: 1px solid #D12026;
      /*  border-bottom: 3px solid #D12026;*/
    }
    .email-box_success {
        border: 3px solid #5cb85c;
        /*border-bottom: 3px solid #5cb85c;*/
    }

    .hr-load {
        display: flex;
        flex-direction: row;
        padding-top: 30px;
    }

    .hr-load:before,
    .hr-load:after {
        content: "";
        flex: 1 1;
        border-bottom: 1px solid #ebebeb;
        margin: auto;
    }
    .noContent{
        text-align: center;
        display: flex;
    }
    .noContent:before,
    .noContent:after {
        content: "";
        flex: 1 1;
        border-bottom: 1px solid #ebebeb;
        margin: auto;
    }
    .waviy {
        position: relative;
        -webkit-box-reflect: below -20px linear-gradient(transparent, rgba(0,0,0,.2));
        font-size: 60px;
    }
    .waviy span {
        font-family: 'Alfa Slab One', cursive;
        position: relative;
        display: inline-block;
        color: #fff;
        text-transform: uppercase;
        animation: waviy 1s infinite;
        animation-delay: calc(.1s * var(--i));

    }
    @keyframes waviy {
        0%,40%,100% {
            transform: translateY(0)
        }
        20% {
            transform: translateY(-20px)
        }
    }


    #error_news{
        padding-top: -10px;
    }

    .form-control:focus {
        color: #666666;
        background-color: #fff;
        border-color: #D12026;
        outline: 0;
        -webkit-box-shadow: 0 0 0 0.25rem rgb(255 69 69 / 25%);
        box-shadow: 0 0 0 0.25rem rgb(255 69 69 / 25%);
    }
    .input-item_error{
        background: #fff;
        border: 1px solid #D12026 !important;
        border-radius: 0;
        height: 42px;
        width: 100%;
        padding: 10px;
        font-size: 14px;}

   .single-input-item-error input {
        background: #fff none repeat scroll 0 0;
        border-radius: 0;
        -webkit-box-shadow: none;
        box-shadow: none;
        color: #212121;
        font-size: 14px;
        height: 40px;
        margin-bottom: 20px;
        padding-left: 10px;
        padding-right: 10px;
        width: 100%;
        border: 2px solid #D12026;
    }


   /*  Review Stars */
    .stars{
        height: 100px;
        margin-top: -30px;

    }

    .stars input{
        display: none;
    }

    .stars label{
        float: right;
        font-size: 20px;
        color: lightgrey;
        margin: 0 5px;
        text-shadow: 1px 1px #bbb;
    }

    .stars label:before{
        content: '★';
        float: left;
    }

    .stars input:checked ~ label{
        color: gold;
        text-shadow: 1px 1px #c60;
    }

    .stars:not(:checked) > label:hover,
    .stars:not(:checked) > label:hover ~ label{
        color: gold;
    }

    .stars input:checked > label:hover,
    .stars input:checked > label:hover ~ label{
        color: gold;
        text-shadow: 1px 1px goldenrod;
    }

    .stars .result:before{
        position: absolute;
        content: "";


        transform: translateX(-47%);
        bottom: -30px;
        font-size: 30px;
        font-weight: 500;
        color: gold;
        font-family: 'Poppins', sans-serif;
        display: none;
    }




    .row_star {
        float: left;
        display: block;
        --bs-gutter-x: 1.875rem;
        --bs-gutter-y: 0;
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-flex-wrap: wrap;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        margin-top: calc(var(--bs-gutter-y) * -1);
        margin-right: calc(var(--bs-gutter-x) / -2);
        margin-left: calc(var(--bs-gutter-x) / -2);
        margin-right: -170px;

    }

#top-mr{
    margin-top: 60px;
}


.comment-error textarea{
    width: 100%;
    border: 2px solid #D12026;
    padding: 0 10px;
    height: 140px;
}
    .modal-content-v {
        position: relative;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: column;
        flex-direction: column;
        width: 100%;
        pointer-events: auto;
        background-color: #fff;
        background-clip: padding-box;
        /* border: 1px solid rgba(0,0,0,.2); */
        border-radius: .3rem;
        outline: 0;
    }
.checkout-border{
    border: 1px solid #D12026 !important;
}
    .select2-selection__rendered {
        line-height: 40px !important;
    }
    .select2-container .select2-selection--single {
        height: 40px !important;
    }
    .select2-selection__arrow {
        height: 34px !important;
    }
   /* .cell{
        width: 95vmax;
    }*/



    .cell{
        width:405px;
    }
    /* Extra small devices (phones, 600px and down) */
    @media only screen and (min-width: 576px) {
        .cell{
            width: 1000px;
        }

    } /*@media only screen and (max-width: 600px) {
        .cell{
            width: 87vmax;
        }

    }*/

    /* Small devices (portrait tablets and large phones, 600px and up) */
    @media only screen and (min-width: 600px) {
        .cell{
            width: 85vmax;
        }

    }

    /* Medium devices (landscape tablets, 768px and up) */
    @media only screen and (min-width: 768px) {
        .cell{
            width: 90vmax;
        }

    }

    /* Large devices (laptops/desktops, 992px and up) */
    @media only screen and (min-width: 992px) {
        .cell{
            width: 45vmax;
        }
    }

    /* Extra large devices (large laptops and desktops, 1200px and up) */
    @media only screen and (min-width: 1200px) {
        .cell{
            width: 48vmax;
        }
    }
    @media only screen and (min-width: 1140px) {
        .cell{
            width: 40vmax;
        }
    }

    .loading {
        position: fixed;
        z-index: 999;
        height: 2em;
        width: 2em;
        overflow: show;
        margin: auto;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
    }

    /* Transparent Overlay */
    .loading:before {
        content: '';
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(rgba(20, 20, 20,.8), rgba(0, 0, 0, .8));

        background: -webkit-radial-gradient(rgba(20, 20, 20,.8), rgba(0, 0, 0,.8));
    }

    /* :not(:required) hides these rules from IE9 and below */
    .loading:not(:required) {
        /* hide "loading..." text */
        font: 0/0 a;
        color: transparent;
        text-shadow: none;
        background-color: transparent;
        border: 0;
    }

    .loading:not(:required):after {
        content: '';
        display: block;
        font-size: 10px;
        width: 1em;
        height: 1em;
        margin-top: -0.5em;
        -webkit-animation: spinner 150ms infinite linear;
        -moz-animation: spinner 150ms infinite linear;
        -ms-animation: spinner 150ms infinite linear;
        -o-animation: spinner 150ms infinite linear;
        animation: spinner 150ms infinite linear;
        border-radius: 0.5em;
        -webkit-box-shadow: rgba(255,255,255, 0.75) 1.5em 0 0 0, rgba(255,255,255, 0.75) 1.1em 1.1em 0 0, rgba(255,255,255, 0.75) 0 1.5em 0 0, rgba(255,255,255, 0.75) -1.1em 1.1em 0 0, rgba(255,255,255, 0.75) -1.5em 0 0 0, rgba(255,255,255, 0.75) -1.1em -1.1em 0 0, rgba(255,255,255, 0.75) 0 -1.5em 0 0, rgba(255,255,255, 0.75) 1.1em -1.1em 0 0;
        box-shadow: rgba(255,255,255, 0.75) 1.5em 0 0 0, rgba(255,255,255, 0.75) 1.1em 1.1em 0 0, rgba(255,255,255, 0.75) 0 1.5em 0 0, rgba(255,255,255, 0.75) -1.1em 1.1em 0 0, rgba(255,255,255, 0.75) -1.5em 0 0 0, rgba(255,255,255, 0.75) -1.1em -1.1em 0 0, rgba(255,255,255, 0.75) 0 -1.5em 0 0, rgba(255,255,255, 0.75) 1.1em -1.1em 0 0;
    }

    /* Animation */

    @-webkit-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @-moz-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @-o-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }

    /* Turn the existing ajaxLoaderHorizontal into a full overlay when placed
   inside the quickview modal, without touching its original keyframes
   or dot styling used elsewhere on the site. */
.modal-content-v .quickview-loader-overlay,
.modalquickview .quickview-loader-overlay {
    position: absolute;
    inset: 0;
    z-index: 20;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.9);
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

.quickview-loader-overlay.hidden {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
}

.quickview-loader-overlay #ajaxLoaderHorizontal {
    margin: 0;
}

#quickview-body {
    transition: opacity 0.2s ease;
}

#quickview-body.loading {
    opacity: 0;
}

#quickview-customizations-list label.badge {
    position: relative;
    transition: background-color .15s ease, border-color .15s ease, color .15s ease;
}

#quickview-customizations-list label.badge input {
    /* Hide the native control everywhere it might otherwise render (or
       fail to render) — we drive the visible state entirely via the
       .selected class below instead. */
    position: absolute;
    opacity: 0;
    width: 1px;
    height: 1px;
    pointer-events: none;
}

#quickview-customizations-list label.badge.selected {
    background-color: #D12026 !important;
    border-color: #D12026 !important;
    color: #fff !important;
}

#quickview-customizations-list label.badge.selected .text-danger {
    color: #fff !important;
}

/* ==========================================================================
   Brand color pass — added by Claude
   Every #b22b40 in this file (80 instances) has been changed to #D12026,
   the actual red sampled from your logo file, so the site's accent color
   now matches your logo/signage instead of a slightly different maroon.

   The rules below only touch selectors that are CONFIRMED to exist in this
   file (.product .thumb .badges span.sale, .product .thumb .actions
   .action). Your full product-card layout (white background, title,
   price, rating stars) is defined in style.min.css, which wasn't shared,
   so I can't safely restyle that card to the dark theme we mocked up
   without guessing at class names. If you want the dark-card menu look
   applied for real, share style.min.css or the product-card partial and
   I'll wire it up precisely instead of guessing.
   ========================================================================== */

.product .thumb .badges span.sale {
    background-color: #D12026;
    color: #fff;
}

.product .thumb .actions .action:hover:not(.active),
.product .thumb .actions .action:hover {
    background-color: #D12026 !important;
    color: #fff;
}

 </style>
    @stack('styles')
</head>
<!-- @include('storefront.partials.delivery-check-widget') -->

<body>

    @include('storefront.partials.header')

    @yield('content')

    @include('storefront.partials.footer')
    @include('storefront.partials.quickview-modal')

    <!-- Scroll Top -->
    <a href="#" class="scroll-top" id="scroll-top">
        <i class="arrow-top fa fa-long-arrow-up"></i>
        <i class="arrow-bottom fa fa-long-arrow-up"></i>
    </a>

    <script src="{{ asset('storefront/assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('storefront/assets/js/plugins.min.js') }}"></script>
    <script src="{{ asset('storefront/assets/js/main.js') }}?v={{ filemtime(public_path('storefront/assets/js/main.js')) }}"></script>
<script>
(function ($) {
    "use strict";

    let quickviewSwiper = null;
    let quickviewBasePrice = 0;
    let quickviewOldPrice = null;

    /* ==========================================
       PRICE CALCULATOR
    ========================================== */
    function renderPriceBox() {

        let total = quickviewBasePrice;

        $('#quickview-customizations-list input:checked').each(function () {
            total += parseFloat($(this).data('price-delta')) || 0;
        });

        let html = `<span class="regular-price">£${total.toFixed(2)}</span>`;

        if (quickviewOldPrice !== null) {
            html += `<span class="old-price"><del>£${quickviewOldPrice.toFixed(2)}</del></span>`;
        }

        $('#quickview-price-box').html(html);
    }

    /* ==========================================
       OPEN QUICKVIEW
    ========================================== */

    $(document).on('click', '.quickview', function () {


    
        const slug   = $(this).data('slug');
        const dealId = $(this).data('deal-id');

        if (!slug && !dealId) return;

        $('#quickview-loader').removeClass('hidden');
        $('#quickview-body').addClass('loading');

        $('#quickview-name').text('');
        $('#quickview-description').text('');
        $('#quickview-price-box').empty();
        $('#quickview-images').empty();

        $('#quickview-spice').hide();
        $('#quickview-dietary').hide();

        $('#quickview-countdown-wrap').hide();
        $('#quickview-countdown').empty();

        $('#quickview-customizations-list').empty();
        $('#quickview-customizations').addClass('d-none');

        /* Deal OR Dish */
        const endpoint = dealId
            ? `/deals/${dealId}/quickview`
            : `/dish/${slug}/quickview`;

        fetch(endpoint, {
            headers: {
                "Accept":"application/json"
            }
        })
        .then(response => {
            if (!response.ok) throw new Error();
            return response.json();
        })
        .then(function (data) {

            /* ===========================
               BASIC INFO
            =========================== */

            $('#quickview-name').text(data.name);

            $('#quickview-description').text(
                data.description ?? ''
            );

            $('#quickview-sku').text(
                'Ref: ' + (data.slug ?? data.deal_id)
            );

            $('#quickview-view-link').attr(
                'href',
                data.url ?? '#'
            );

            $('#quickview-wishlist-link').attr(
                'href',
                data.wishlist_url ?? '#'
            );

            /* ===========================
               PRICE
            =========================== */

            if (data.deal_base_price !== null && data.deal_base_price !== undefined) {

                quickviewBasePrice = parseFloat(data.deal_base_price);
                quickviewOldPrice  = parseFloat(data.base_price);

            } else if (data.is_on_sale) {

                quickviewBasePrice = parseFloat(data.discount_price);
                quickviewOldPrice  = parseFloat(data.base_price);

            } else {

                quickviewBasePrice = parseFloat(data.base_price);
                quickviewOldPrice  = null;
            }

            renderPriceBox();

            /* ===========================
               COUNTDOWN
            =========================== */

            if (data.deal_countdown) {

                $('#quickview-countdown-wrap').show();

                const $cd = $('#quickview-countdown');

                try {
                    $cd.countdown('destroy');
                } catch (e) {}

                $cd.countdown(data.deal_countdown, function (event) {

                    $cd.html(event.strftime(
                        '<div class="single-countdown">' +
                        '<span class="single-countdown_time">%D</span>' +
                        '<span class="single-countdown_text">Days</span>' +
                        '</div>' +

                        '<div class="single-countdown">' +
                        '<span class="single-countdown_time">%H</span>' +
                        '<span class="single-countdown_text">Hours</span>' +
                        '</div>' +

                        '<div class="single-countdown">' +
                        '<span class="single-countdown_time">%M</span>' +
                        '<span class="single-countdown_text">Min</span>' +
                        '</div>' +

                        '<div class="single-countdown">' +
                        '<span class="single-countdown_time">%S</span>' +
                        '<span class="single-countdown_text">Sec</span>' +
                        '</div>'
                    ));

                });

            }

            /* ===========================
               SPICE
            =========================== */

            if (data.spice_level) {

                $('#quickview-spice-value').text(data.spice_level);

                $('#quickview-spice').show();
            }

            /* ===========================
               DIETARY
            =========================== */

            if (data.dietary && data.dietary.length) {

                $('#quickview-dietary-value').text(
                    data.dietary.join(', ')
                );

                $('#quickview-dietary').show();
            }

            /* ===========================
               IMAGES
            =========================== */

            const wrapper = $('#quickview-images');
            wrapper.empty();

            if (data.images && data.images.length) {

                data.images.forEach(function (image) {

                    wrapper.append(`
                        <a class="swiper-slide" href="${image}">
                            <img src="${image}" class="w-100" alt="${data.name}">
                        </a>
                    `);

                });

            }

            if (quickviewSwiper) {
                quickviewSwiper.destroy(true, true);
            }

            quickviewSwiper = new Swiper(
                '.modal-product-carousel .swiper-container',
                {
                    loop: data.images.length > 1,
                    slidesPerView: 1,
                    spaceBetween: 0,

                    navigation: {
                        nextEl: '.modal-product-carousel .swiper-product-button-next',
                        prevEl: '.modal-product-carousel .swiper-product-button-prev',
                    }
                }
            );

            /* ===========================
               CUSTOMIZATION OPTIONS
            =========================== */

            const customBox = $('#quickview-customizations-list');
            customBox.empty();

            $('#quickview-customizations').addClass('d-none');

            if (data.option_groups && data.option_groups.length) {

                $('#quickview-customizations').removeClass('d-none');

                data.option_groups.forEach(function (group) {

                    const inputType = group.multiple ? 'checkbox' : 'radio';
                    const groupName = `quickview_group_${group.id}`;

                    let html = `
                        <div class="mb-3">

                            <strong class="d-block mb-2">
                                ${group.name}

                                <span class="text-muted small fw-normal">
                                    (${group.multiple ? 'Choose Multiple' : 'Choose One'}${group.required ? ', Required' : ''})
                                </span>

                            </strong>

                            <div class="d-flex flex-wrap gap-2">
                    `;

                    group.values.forEach(function (value, index) {

                        const price = parseFloat(value.price_delta ?? 0);

                        const checked =
                            (!group.multiple && group.required && index === 0)
                            ? 'checked'
                            : '';

                        const selected =
                            checked ? 'selected' : '';

                        const inputId =
                            `qv_${group.id}_${value.id}`;

                        html += `
                            <label
                                for="${inputId}"
                                class="badge bg-light border text-dark px-3 py-2 rounded-pill ${selected}"
                                style="cursor:pointer"
                            >

                                <input
                                    id="${inputId}"
                                    type="${inputType}"
                                    name="${groupName}"
                                    data-price-delta="${price}"
                                    ${checked}
                                    ${group.required && !group.multiple ? 'required' : ''}
                                >

                                ${value.name}

                                ${price > 0
                                    ? `<span class="text-danger ms-1">+£${price.toFixed(2)}</span>`
                                    : ''}

                            </label>
                        `;

                    });

                    html += `
                            </div>
                        </div>
                    `;

                    customBox.append(html);

                });

                customBox.off('change').on('change','input',function(){

                    const input = $(this);

                    const label = input.closest('label');

                    if (input.attr('type') === 'radio') {

                        label.closest('.d-flex')
                            .find('label')
                            .removeClass('selected');

                        label.addClass('selected');

                    } else {

                        label.toggleClass(
                            'selected',
                            input.prop('checked')
                        );

                    }

                    renderPriceBox();

                });

                renderPriceBox();

            }

            /* ===========================
               ADD TO CART BUTTON
            =========================== */

            const button = $('#quickview-add-cart');

            if (button.length) {

                button
                    .attr('data-menu-item-id', data.id ?? '')
                    .attr('data-deal-id', data.deal_id ?? '');

                if (data.deal_id) {

                    button
                        .removeClass('js-add-to-cart')
                        .addClass('js-add-bundle-to-cart');

                } else {

                    button
                        .removeClass('js-add-bundle-to-cart')
                        .addClass('js-add-to-cart');

                }

            }

            $('#quickview-loader').addClass('hidden');
            $('#quickview-body').removeClass('loading');

        })
        .catch(function(){

            $('#quickview-loader').addClass('hidden');
            $('#quickview-body').removeClass('loading');

            $('#quickview-name').text(
                'Unable to load this item.'
            );

        });

    });

    /* ==========================================
       CLOSE MODAL
    ========================================== */

    $(document).on(
        'hidden.bs.modal',
        '#exampleModalCenter',
        function () {

            try {
                $('#quickview-countdown').countdown('destroy');
            } catch (e) {}

        }
    );

})(jQuery);
</script>

    @include('storefront.partials.cart-scripts')

    @stack('scripts')

</body>

</html>