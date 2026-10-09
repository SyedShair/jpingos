<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingEvent;
use App\Models\BusinessHour;
use Illuminate\Support\Facades\Auth;

/**
 * Renders the booking pages only. The actual create/read/update/delete
 * operations go through the JSON API — see App\Http\Controllers\Api\BookingController
 * and routes/api.php.
 */
class BookingController extends Controller
{
    /** GET /book — shows current events as cards, or a plain form if none are running. */
    public function index()
    {
        $events = BookingEvent::currentlyRunning()->get()->reject->isFull()->values();

        return view('storefront.booking', [
            'events'   => $events,
            'customer' => Auth::guard('customer')->user(),
            'hours'    => BusinessHour::forFrontend(),
        ]);
    }

    /** GET /book/{booking}/confirmation — shown after a successful POST /api/bookings. */
    public function confirmation(Booking $booking)
    {
        return view('storefront.booking-confirmation', ['booking' => $booking->load('event')]);
    }
}
