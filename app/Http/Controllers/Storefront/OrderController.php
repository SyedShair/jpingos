<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

            // dining = a pre-order table reservation: no address, no fee.
            'order_type' => ['required', 'in:pickup,delivery,dining'],

            'delivery_fee' => ['nullable', 'numeric', 'min:0'],

            'is_pre_order'   => ['boolean'],
            'pre_order_date' => ['required_if:is_pre_order,true', 'nullable', 'date', 'after_or_equal:today'],
            'pre_order_time' => ['required_if:is_pre_order,true', 'nullable', 'date_format:H:i'],

            // Address fields are required only for delivery orders.
            // Pickup and dining send them as null.
            'address'   => ['required_if:order_type,delivery', 'nullable', 'string', 'max:255'],
            'apartment' => ['nullable', 'string', 'max:255'],
            'city'      => ['required_if:order_type,delivery', 'nullable', 'string', 'max:100'],
            'postcode'  => ['required_if:order_type,delivery', 'nullable', 'string', 'max:20'],
            'notes'     => ['nullable', 'string', 'max:1000'],

            'payment_method' => ['required', 'in:cod,card'],
        ]);

        // Card isn't wired to any gateway yet — reject explicitly rather
        // than silently accepting it and creating an order stuck in a
        // half-paid state. Remove this guard once a gateway integration
        // exists; everything below (the payments table, the Payment row
        // created in the transaction) already supports it.
        if ($data['payment_method'] === 'card') {
            return response()->json([
                'message' => 'Card payments aren\'t available yet — please select Cash on Delivery.',
            ], 422);
        }

        $cartItems = $this->cart->detailedItems();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'Your cart is empty.',
            ], 422);
        }

        $subtotal = $this->cart->subtotal();

        // Only delivery orders carry a fee. The fee itself comes from the
        // live postcode lookup on the checkout page; pickup, dining and
        // pre-order delivery are all zero.
        $deliveryFee = $data['order_type'] === 'delivery'
            ? (float) ($data['delivery_fee'] ?? 0)
            : 0.0;

        $total = $subtotal + $deliveryFee;

        $order = DB::transaction(function () use ($data, $cartItems, $subtotal, $deliveryFee, $total) {

            $order = Order::create([
                // Works for both guest and logged-in customer: a guest
                // simply has no session on the "customer" guard, so this
                // resolves to null, which the orders table allows.
                'customer_id' => Auth::guard('customer')->id(),

                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'email'      => $data['email'],
                'phone'      => $data['phone'],
                'address'    => $data['address'] ?? null,
                'apartment'  => $data['apartment'] ?? null,
                'city'       => $data['city'] ?? null,
                'postcode'   => $data['postcode'] ?? null,
                'order_type' => $data['order_type'],
                'notes'      => $data['notes'] ?? null,

                'is_pre_order'   => $data['is_pre_order'] ?? false,
                'pre_order_date' => $data['pre_order_date'] ?? null,
                'pre_order_time' => $data['pre_order_time'] ?? null,

                'subtotal'     => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total'        => $total,
                'status'       => 'pending',
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

            Payment::create([
                'order_id' => $order->id,
                'method'   => $data['payment_method'],
                // COD is "pending" until someone actually collects the
                // cash — a separate admin action (mark-as-paid), not
                // something this endpoint can know at placement time.
                'status'   => 'pending',
                'amount'   => $total,
                'currency' => 'GBP',
            ]);

            return $order;
        });

        $this->cart->clear();

        // Mail failures shouldn't take down a successful order — the
        // customer has already paid the cart with their time, and the
        // JS front-end only knows "ok" or "error".
        try {
            $adminEmail = Setting::find(1);

            Mail::to($order->email)
                ->when(! empty($adminEmail->email), fn ($mail) => $mail->cc($adminEmail->email))
                ->send(new OrderConfirmationMail($order));
        } catch (\Throwable $e) {
            Log::error('Order confirmation mail failed', [
                'order_number' => $order->order_number,
                'error'        => $e->getMessage(),
            ]);
        }

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

        $categories = \App\Models\Category::topLevel()
                ->active()
                ->ordered()
                ->with(['children' => fn ($q) => $q->active()->ordered()])
                ->get();

        return view('storefront.order-confirmation', [
            'order' => $order->load(['items', 'latestPayment']),
            'categories' => $categories,
        ]);
    }
}