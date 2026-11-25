<?php

// Save as: app/Http/Controllers/OrderPrintController.php  (replace your current file)

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderReceiptPrinter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderPrintController extends Controller
{
    /**
     * GET  orders.print
     * A printable page for the browser (A4 or thermal layout, opened in a new tab).
     * This NEVER sends anything to the printer by itself.
     */
    public function show(Request $request, Order $order)
    {
        $format = $request->query('format', 'thermal');
        $format = in_array($format, ['thermal', 'a4'], true) ? $format : 'thermal';

        // Thermal paper comes in 58mm and 80mm. Anything else falls back to 80.
        $width = (int) $request->query('width', 80);
        $width = in_array($width, [58, 80], true) ? $width : 80;

        // Eager-load so the receipt doesn't trigger lazy queries per print.
        $order->load(['items', 'latestPayment']);

        return view('admin.orders.print', [
            'order'  => $order,
            'format' => $format,
            'width'  => $width,
        ]);
    }

    /**
     * POST orders.print.escpos
     * Sends the receipt straight to the Star printer and answers with JSON,
     * which the page turns into a toast message.
     */
    public function escpos(Request $request, Order $order, OrderReceiptPrinter $printer): JsonResponse
    {
        $width = (int) $request->input('width', 80);
        $width = in_array($width, [58, 80], true) ? $width : 80;

        $order->load(['items', 'latestPayment']);

        try {
            $printer->print($order, $width);
        } catch (\Throwable $e) {
            report($e); // full technical error goes to storage/logs/laravel.log

            return response()->json([
                'ok'      => false,
                'message' => "Couldn't print order {$order->order_number}. Check the printer is on, connected and has paper.",
                // Technical detail only while APP_DEBUG=true
                'detail'  => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        return response()->json([
            'ok'      => true,
            'message' => "Order {$order->order_number} sent to the printer.",
        ]);
    }
}