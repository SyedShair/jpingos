<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\BusinessHour;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * REST API for bookings.
 *
 *   POST   /api/bookings        create (validates → GuestPlan → saves locally)
 *   GET    /api/bookings        list, paginated, filterable
 *   GET    /api/bookings/{id}   show one
 *   PUT    /api/bookings/{id}   update (DB only — never calls GuestPlan)
 *   PATCH  /api/bookings/{id}   update (DB only — never calls GuestPlan)
 *   DELETE /api/bookings/{id}   soft-delete (DB only — never cancels in GuestPlan)
 *
 * Every response uses the {success, data|error} envelope. Internal details —
 * stack traces, the GuestPlan access key, raw GuestPlan error bodies beyond a
 * safe message — are never returned to the client.
 */
class BookingController extends Controller
{
    public function __construct(private BookingService $bookings)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $paginated = $this->bookings->list(
            $request->only(['date', 'status', 'customer_name', 'email', 'phone', 'guestplan_booking_id']),
            (int) $request->query('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'data'    => BookingResource::collection($paginated->items()),
            'meta'    => [
                'currentPage' => $paginated->currentPage(),
                'perPage'     => $paginated->perPage(),
                'total'       => $paginated->total(),
                'lastPage'    => $paginated->lastPage(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $booking = $this->bookings->find($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->notFound();
        }

        return response()->json(['success' => true, 'data' => new BookingResource($booking)]);
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Authoritative opening-hours check — the booking page only offers in-hours
        // options, but a request can always bypass the UI.
        if (! BusinessHour::isOpenAt($data['booking_date'], $data['booking_time'])) {
            $dow = \Illuminate\Support\Carbon::parse($data['booking_date'])->dayOfWeek;

            return response()->json([
                'success' => false,
                'error'   => [
                    'code'    => 'OUTSIDE_OPENING_HOURS',
                    'message' => "We're closed at that time. Hours for " . BusinessHour::DAY_NAMES[$dow] . ': ' . BusinessHour::describe($dow),
                ],
            ], 422);
        }

        $idempotencyKey = $request->header('Idempotency-Key') ?? $data['idempotency_key'] ?? null;

        try {
            $booking = $this->bookings->createBooking($data, $idempotencyKey);
        } catch (\Throwable $e) {
            report($e); // logged for you — never shown to the client

            return response()->json([
                'success' => false,
                'error'   => ['code' => 'BOOKING_FAILED', 'message' => 'Unable to create booking.'],
            ], 500);
        }

        // Booking is always saved locally even if GuestPlan rejected it (status "failed"),
        // so this always returns 201 with the booking's real status — the frontend
        // decides how to present a "failed" status rather than getting a hard error.
        return response()->json(['success' => true, 'data' => new BookingResource($booking)], 201);
    }

    public function update(UpdateBookingRequest $request, int $id): JsonResponse
    {
        try {
            $booking = $this->bookings->find($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->notFound();
        }

        $booking = $this->bookings->update($booking, $request->validated());

        return response()->json(['success' => true, 'data' => new BookingResource($booking)]);
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $booking = $this->bookings->find($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->notFound();
        }

        $this->bookings->delete($booking);

        return response()->json(['success' => true, 'data' => null]);
    }

    private function notFound(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error'   => ['code' => 'BOOKING_NOT_FOUND', 'message' => 'Booking not found.'],
        ], 404);
    }
}
