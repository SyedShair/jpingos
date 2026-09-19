<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderPrintController extends Controller
{
    public function show(Request $request, Order $order)
    {
        $format = $request->query('format', 'thermal');
        $format = in_array($format, ['thermal', 'a4'], true) ? $format : 'thermal';

        // Thermal paper is sold in a handful of fixed widths — 58mm and
        // 80mm cover the vast majority of receipt printers. Anything
        // outside that pair is ignored rather than trusted as-is, since
        // it drives an actual @page CSS size and a bogus value would
        // just print garbage.
        $width = (int) $request->query('width', 80);
        $width = in_array($width, [58, 80], true) ? $width : 80;

        // latestPayment is eager-loaded so the receipt's payment block
        // doesn't trigger a lazy query per print (and still works if
        // lazy loading is ever disabled app-wide).
        $order->load(['items', 'latestPayment']);

        return view('admin.orders.print', [
            'order'  => $order,
            'format' => $format,
            'width'  => $width,
        ]);
    }
}