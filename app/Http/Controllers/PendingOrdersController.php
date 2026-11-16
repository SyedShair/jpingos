<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;

/**
 * Backs the header's "Pending Orders" dropdown — see
 * resources/views/layouts/partials/header.blade.php, which polls this every
 * 20 seconds, and AppServiceProvider::boot() where the SAME query runs once
 * on normal page loads (so it shows correctly even before the first poll).
 */
class PendingOrdersController extends Controller
{
    public function poll(): JsonResponse
    {
        $pendingOrders = Order::where('status', 'pending')->latest()->limit(6)->get();

        return response()->json([
            'count' => Order::where('status', 'pending')->count(),
            'html'  => view('layouts.partials.pending-orders-list', compact('pendingOrders'))->render(),
        ]);
    }
}
