<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function store(Request $request)
    {
       $data = $request->validate([
    'first_name' => ['required', 'string', 'max:100'],
    'last_name'  => ['required', 'string', 'max:100'],
    'email'      => ['required', 'email', 'max:255'],
    'phone'      => ['required', 'string', 'max:20'],
    'order_type' => ['required', 'in:pickup,delivery'],

    // These are now required only for delivery orders — pickup sends
    // them as null, since there's no address to collect.
    'address'    => ['required_if:order_type,delivery', 'nullable', 'string', 'max:255'],
    'apartment'  => ['nullable', 'string', 'max:255'],
    'city'       => ['required_if:order_type,delivery', 'nullable', 'string', 'max:100'],
    'postcode'   => ['required_if:order_type,delivery', 'nullable', 'string', 'max:20'],
    'notes'      => ['nullable', 'string', 'max:1000'],
]);
        $cartItems = $this->cart->detailedItems();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'Your cart is empty.',
            ], 422);
        }

        $subtotal = $this->cart->subtotal();

        // NOTE: no delivery-fee/shipping logic exists anywhere in this
        // app yet — the checkout page shows "Calculated at next step"
        // and never actually calculates one. Total = subtotal for now.
        $total = $subtotal;

        $order = DB::transaction(function () use ($data, $cartItems, $subtotal, $total) {

            $order = Order::create([
                // Works for both guest and logged-in customer: a guest
                // simply has no session on the "customer" guard, so this
                // resolves to null, which the orders table allows.
                'customer_id' => Auth::guard('customer')->id(),

                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'email'      => $data['email'],
                'phone'      => $data['phone'],
                'address'    => $data['address'],
                'apartment'  => $data['apartment'] ?? null,
                'city'       => $data['city'],
                'postcode'   => $data['postcode'] ?? null,
                'order_type' => $data['order_type'],
                'notes'      => $data['notes'] ?? null,

                'subtotal' => $subtotal,
                'total'    => $total,
                'status'   => 'pending',
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'menu_item_id' => $cartItem->is_bundle ? null : $cartItem->menuItem->id,
                    'deal_id'      => $cartItem->deal_id,
                    'name'         => $cartItem->menuItem->name,
                    'options'      => $cartItem->options->isNotEmpty()
                        ? $cartItem->options->map(fn ($o) => [
                            'name'        => $o->name,
                            'price_delta' => (float) $o->price_delta,
                        ])->values()->all()
                        : null,
                    'unit_price' => $cartItem->unit_price,
                    'quantity'   => $cartItem->quantity,
                    'line_total' => $cartItem->line_total,
                ]);
            }

            return $order;
        });

        $this->cart->clear();
        Mail::to($order->email)->send(new OrderConfirmationMail($order));



        return response()->json([
            'order_number'     => $order->order_number,
            'confirmation_url' => route('storefront.order.confirmation', $order->order_number),
        ]);
    }

    public function confirmation(Order $order)
{
    // Guest orders (customer_id is null) stay reachable by anyone with
    // the link — there's no account to check against, same as most
    // guest-checkout confirmation pages.
    //
    // Orders placed while logged in ARE locked to that customer: only
    // the account that placed it can view it. Guessing/sharing the URL
    // isn't enough on its own for those.
    if ($order->customer_id !== null) {
        abort_unless(
            $order->customer_id === Auth::guard('customer')->id(),
            403
        );
    }

    return view('storefront.order-confirmation', [
        'order' => $order->load('items'),
    ]);
}

    // public function confirmation(Order $order)
    // {
    //     // No ownership check: order_number is an 8-char random string
    //     // (not sequential/guessable), and guest orders have no
    //     // customer_id to check against anyway. If you want it locked to
    //     // the logged-in customer who placed it when there IS one, add:
    //     // abort_unless($order->customer_id === auth('customer')->id(), 403);
    //     return view('storefront.order-confirmation', [
    //         'order' => $order->load('items'),
    //     ]);
    // }
}