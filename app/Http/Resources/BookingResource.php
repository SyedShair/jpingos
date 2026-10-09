<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => (string) $this->id,
            'bookingNumber'      => $this->booking_number,
            'guestplanBookingId' => $this->guestplan_booking_id,
            'eventName'          => $this->whenLoaded('event', fn () => $this->event?->name),
            'customerName'       => $this->customer_name,
            'customerEmail'      => $this->customer_email,
            'customerPhone'      => $this->customer_phone,
            'bookingDate'        => $this->booking_date?->toDateString(),
            'bookingTime'        => substr((string) $this->booking_time, 0, 5),
            'partySize'          => $this->party_size,
            'notes'              => $this->notes,
            'status'             => $this->status,
            'createdAt'          => $this->created_at?->toIso8601String(),
            'updatedAt'          => $this->updated_at?->toIso8601String(),
            // Deliberately NOT included: guestplan_error, idempotency_key, restaurant_id,
            // customer_id, deleted_at — internal-only fields never returned by the API.
        ];
    }
}
