<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\CartService;

class CheckoutController extends Controller
{
    public function index(CartService $cart)
    {
        $items = $cart->detailedItems();

        if ($items->isEmpty()) {
            return redirect()
                ->route('storefront.home')
                ->with('status', 'Your order is empty — add a dish before checking out.');
        }

        return view('storefront.checkout', [
            'cartItems'    => $items,
            'cartSubtotal' => $cart->subtotal(),
        ]);
    }
}