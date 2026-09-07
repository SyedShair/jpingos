<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Services\CartService;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('storefront.partials.header', function ($view) {
        $cart = app(CartService::class);

        $view->with([
            'cartItems'    => $cart->detailedItems(),
            'cartSubtotal' => $cart->subtotal(),
            'cartCount'    => $cart->count(),
        ]);
    });
    }
}
