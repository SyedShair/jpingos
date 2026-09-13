<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class TrackOrderController extends Controller
{
    public function index()
    {
        return view('storefront.track-order');
    }

    public function lookup(Request $request)
    {
        $data = $request->validate([
            'order_number' => ['required', 'string'],
            'email'        => ['required', 'email'],
        ]);

        // Requiring email to match, not just the order number, stops
        // someone who merely glimpsed a number on a receipt/screen from
        // pulling up full order details without also knowing the email
        // it was placed under.
        $order = Order::where('order_number', trim($data['order_number']))
            ->where('email', $data['email'])
            ->with('items')
            ->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'We couldn\'t find an order matching that order number and email.',
            ]);
        }

        return response()->json([
            'success' => true,
            'html'    => view('storefront.partials.order-tracking-result', ['order' => $order])->render(),
        ]);
    }
}