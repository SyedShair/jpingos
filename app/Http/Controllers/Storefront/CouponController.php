<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\LoyaltyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct(
        protected CartService $cart,
        protected LoyaltyService $loyalty,
    ) {}

    /**
     * Checkout "Apply" button. A PREVIEW only: it tells the page what the
     * discount would be. Nothing is marked used here, and OrderController
     * re-validates everything when the order is actually placed.
     */
    public function check(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code'  => ['required', 'string', 'max:32'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        // The subtotal comes from the server-side cart, never from the browser.
        $result = $this->loyalty->validateForCheckout($data['code'], $data['email'], (float) $this->cart->subtotal());

        if (! $result['valid']) {
            return response()->json(['valid' => false, 'message' => $result['message']], 422);
        }

        return response()->json([
            'valid'    => true,
            'code'     => $result['coupon']->code,
            'discount' => $result['discount'],
            'message'  => 'Code applied — ' . $result['coupon']->discount_label . '.',
        ]);
    }
}