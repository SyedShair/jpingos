<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Services\CartService;
use App\Models\Order;
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
        // Inside boot():
View::composer('layouts.partials.header', function ($view) {
    $pendingOrders = Order::where('status', 'pending')->latest()->limit(6)->get();
 
    $view->with([
        'pendingOrders'      => $pendingOrders,
        'pendingOrdersCount' => Order::where('status', 'pending')->count(),
    ]);
});
 

    
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
