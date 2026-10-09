<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * All GuestPlan-specific HTTP logic lives here — nowhere else in the app
 * talks to GuestPlan directly. The access key is read from config only and
 * is never sent to, or accepted from, the frontend.
 *
 * CONFIRMED against the real API by direct probing (2026-09-23) — no public
 * developer docs were available to consult, so this was verified empirically:
 *   - Endpoint: POST {base_url}/restaurants/{restaurant_id}/reservations
 *     (config('guestplan.reservation_path'))
 *   - Auth: a header literally named "Authorization" is required — a wrong
 *     header name returned {"code":3001,"description":"No authorization
 *     header."}.
 *
 * STILL UNCONFIRMED:
 *   - The exact value format inside "Authorization" (Bearer <key>, raw key,
 *     etc.) — configurable via GUESTPLAN_AUTH_SCHEME, no code change needed
 *     once known.
 *   - The exact JSON field names this endpoint expects. Every payload shape
 *     tried so far (snake_case, camelCase, flat, nested, with/without
 *     restaurant_id in the body) returns an IDENTICAL generic 500
 *     {"code":9999,"description":"Internal server error."} — which points
 *     away from "wrong field names" and toward restaurant 39148 not being
 *     fully provisioned for reservations on GuestPlan's side yet. That's a
 *     question for GuestPlan support, not something guessable from outside.
 *   buildPayload() below is the ONE place to update once you have a
 *   confirmed field spec from GuestPlan.
 */
class GuestplanService
{
    private function authHeaderValue(): string
    {
        $key = (string) config('guestplan.access_key');

        return match (config('guestplan.auth_scheme')) {
            'raw'    => $key,
            'apikey' => "ApiKey {$key}",
            'token'  => "Token {$key}",
            default  => "Bearer {$key}",
        };
    }

    private function http()
    {
        return Http::baseUrl(rtrim((string) config('guestplan.base_url'), '/'))
            ->withHeaders([
                config('guestplan.auth_header') => $this->authHeaderValue(),
                'Accept'                        => 'application/json',
                'Content-Type'                  => 'application/json',
            ])
            ->timeout((int) config('guestplan.timeout'));
    }

    private function reservationUrl(): string
    {
        return str_replace(
            '{restaurant_id}',
            (string) config('guestplan.restaurant_id'),
            config('guestplan.reservation_path')
        );
    }

    /**
     * Create a reservation in GuestPlan for a booking that has already been
     * saved locally with status "pending".
     *
     * @return array{ok: bool, guestplan_booking_id: ?string, error: ?string}
     */
    public function createBooking(Booking $booking): array
    {
        if (! config('guestplan.base_url') || ! config('guestplan.access_key')) {
            return ['ok' => false, 'guestplan_booking_id' => null, 'error' => 'GuestPlan is not configured (missing base URL or access key).'];
        }

        $payload = $this->buildPayload($booking);
        $url = $this->reservationUrl();

        Log::info('GuestPlan booking request', [
            'url'            => $url,
            'booking_id'     => $booking->id,
            'booking_number' => $booking->booking_number,
        ]);

        try {
            $response = $this->http()->post($url, $payload);

            Log::info('GuestPlan booking response', [
                'status'     => $response->status(),
                'successful' => $response->successful(),
                'body'       => Str::limit($response->body(), 500),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'ok'                    => true,
                    'guestplan_booking_id'  => $data['id'] ?? $data['reservation_id'] ?? $data['reservationId'] ?? null,
                    'error'                 => null,
                ];
            }

            $message = $response->json('message')
                ?? $response->json('description')
                ?? $response->json('Description')
                ?? $response->json('error')
                ?? 'GuestPlan rejected the booking.';

            Log::warning('GuestPlan booking rejected', [
                'status'         => $response->status(),
                'body'           => $response->body(),
                'booking_id'     => $booking->id,
                'booking_number' => $booking->booking_number,
            ]);

            return ['ok' => false, 'guestplan_booking_id' => null, 'error' => $message];
        } catch (ConnectionException|RequestException $e) {
            Log::error('GuestPlan booking request failed', ['message' => $e->getMessage(), 'booking_id' => $booking->id]);

            return ['ok' => false, 'guestplan_booking_id' => null, 'error' => 'Could not reach GuestPlan: ' . $e->getMessage()];
        }
    }

    /** The one place GuestPlan's field names live — edit once you have a confirmed spec. */
    private function buildPayload(Booking $booking): array
    {
        return [
            'restaurant_id' => config('guestplan.restaurant_id'),
            'date'          => $booking->booking_date->toDateString(),
            'time'          => substr((string) $booking->booking_time, 0, 5),
            'party_size'    => $booking->party_size,
            'guest'         => [
                'name'  => $booking->customer_name,
                'email' => $booking->customer_email,
                'phone' => $booking->customer_phone,
            ],
            'notes'     => $booking->notes,
            'reference' => $booking->booking_number,
        ];
    }
}
