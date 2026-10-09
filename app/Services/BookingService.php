<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingEvent;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Orchestrates booking creation/reads/updates/deletes. Controllers call this,
 * not GuestplanService directly — keeps the HTTP layer thin and the
 * GuestPlan-specific code confined to one place (GuestplanService).
 *
 *   BookingController -> BookingService -> GuestplanService -> GuestPlan API
 */
class BookingService
{
    public function __construct(private GuestplanService $guestplan)
    {
    }

    /**
     * Create a booking: validated $data comes in, is saved locally as
     * "pending", sent to GuestPlan, then updated to "confirmed" or "failed"
     * based on the result. Never marks a booking "confirmed" if GuestPlan
     * rejected it.
     *
     * Idempotent: if $idempotencyKey matches an existing booking, that
     * existing booking is returned as-is and GuestPlan is NOT called again —
     * this is what stops a retried/duplicated request from creating two
     * GuestPlan reservations for the same submission.
     */
    public function createBooking(array $data, ?string $idempotencyKey = null): Booking
    {
        if ($idempotencyKey) {
            $existing = Booking::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $existing;
            }
        }

        $event = null;
        if (! empty($data['event_slug'])) {
            $event = BookingEvent::currentlyRunning()->where('slug', $data['event_slug'])->first();
        }

        return DB::transaction(function () use ($data, $event, $idempotencyKey) {
            $booking = Booking::create([
                'idempotency_key'   => $idempotencyKey,
                'restaurant_id'     => (string) config('guestplan.restaurant_id'),
                'booking_event_id'  => $event?->id,
                'customer_id'       => Auth::guard('customer')->id(),
                'customer_name'     => $data['customer_name'],
                'customer_email'    => $data['customer_email'],
                'customer_phone'    => $data['customer_phone'],
                'booking_date'      => $data['booking_date'],
                'booking_time'      => $data['booking_time'],
                'party_size'        => $data['party_size'],
                'notes'             => $data['notes'] ?? null,
                'status'            => 'pending',
            ]);

            // Guestplan is only ever called here — at creation. Updates/cancellations
            // deliberately do not call GuestPlan at this stage (see BookingService::update/delete).
            $result = $this->guestplan->createBooking($booking);

            $booking->update($result['ok']
                ? [
                    'status'                => 'confirmed',
                    'guestplan_booking_id'   => $result['guestplan_booking_id'],
                    'synced_at'              => now(),
                ]
                : [
                    'status'          => 'failed',
                    'guestplan_error' => $result['error'],
                ]
            );

            return $booking->fresh();
        });
    }

    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return Booking::with('event')
            ->when($filters['date'] ?? null, fn ($q, $v) => $q->whereDate('booking_date', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['customer_name'] ?? null, fn ($q, $v) => $q->where('customer_name', 'like', "%{$v}%"))
            ->when($filters['email'] ?? null, fn ($q, $v) => $q->where('customer_email', 'like', "%{$v}%"))
            ->when($filters['phone'] ?? null, fn ($q, $v) => $q->where('customer_phone', 'like', "%{$v}%"))
            ->when($filters['guestplan_booking_id'] ?? null, fn ($q, $v) => $q->where('guestplan_booking_id', $v))
            ->orderByDesc('booking_date')->orderByDesc('booking_time')
            ->paginate($perPage);
    }

    public function find(int $id): Booking
    {
        return Booking::with('event')->findOrFail($id);
    }

    /** Updates the local record ONLY — never calls GuestPlan (per current requirements). */
    public function update(Booking $booking, array $data): Booking
    {
        $booking->update(array_intersect_key($data, array_flip([
            'customer_name', 'customer_email', 'customer_phone',
            'booking_date', 'booking_time', 'party_size', 'notes', 'status',
        ])));

        return $booking->fresh();
    }

    /** Soft-deletes the local record ONLY — never cancels in GuestPlan (per current requirements). */
    public function delete(Booking $booking): bool
    {
        return (bool) $booking->delete();
    }
}
