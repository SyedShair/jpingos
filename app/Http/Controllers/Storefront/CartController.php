<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function store(Request $request)
    {
        $data = $request->validate([
            'menu_item_id' => ['required', 'integer', 'exists:menu_items,id'],
            'quantity'     => ['nullable', 'integer', 'min:1', 'max:20'],
            'options'      => ['nullable', 'array'],
            'options.*'    => ['integer', 'exists:option_values,id'],
        ]);

        $this->cart->add(
            $data['menu_item_id'],
            $data['quantity'] ?? 1,
            $data['options'] ?? []
        );

        return $this->response();
    }

    public function update(Request $request, string $rowId)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:20'],
        ]);

        $this->cart->updateQuantity($rowId, $data['quantity']);

        return $this->response();
    }

    public function destroy(string $rowId)
    {
        $this->cart->remove($rowId);

        return $this->response();
    }

    protected function response()
    {
        return response()->json([
            'count'    => $this->cart->count(),
            'subtotal' => number_format($this->cart->subtotal(), 2),
            'html'     => view('storefront.partials.cart-offcanvas-content', [
                'cartItems'    => $this->cart->detailedItems(),
                'cartSubtotal' => $this->cart->subtotal(),
            ])->render(),
        ]);
    }
}