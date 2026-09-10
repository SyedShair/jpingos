<?php

namespace App\Http\Controllers;

use App\Models\DeliverySetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DeliveryCheckController extends Controller
{
    /**
     * GET /delivery-check — the standalone page hosting the widget.
     */
    public function show(): \Illuminate\View\View
    {
        return view('delivery-check', [
            'setting' => DeliverySetting::current(),
        ]);
    }

    /**
     * POST /delivery-check  { postcode: "SW1A 1AA" }
     *
     * Geocodes the given postcode via Google's Geocoding API, checks eligibility,
     * and calculates dynamic delivery charges.
     */
    public function check(Request $request): JsonResponse
    {
        $data = $request->validate([
            'postcode' => ['required', 'string', 'max:12'],
        ]);

        $coords = $this->geocodePostcode($data['postcode']);

        if (! $coords) {
            return response()->json([
                'available' => false,
                'message'   => "We couldn't find that postcode — please check it and try again.",
                'fee'       => null,
            ], 422);
        }

        $setting = DeliverySetting::current();

        if (! $setting->is_active) {
            return response()->json([
                'available' => false,
                'message'   => 'Sorry, delivery is currently unavailable.',
                'fee'       => null,
            ]);
        }

        $distanceKm = $setting->distanceToKm($coords['lat'], $coords['lng']);
        $available = $distanceKm <= $setting->radius_km;

        // Calculate delivery charge if the destination is within range
        $fee = $available ? $setting->calculateFee($coords['lat'], $coords['lng']) : null;

        return response()->json([
            'available'   => $available,
            'fee'         => $fee !== null ? (float) $fee : null,
            'distance_km' => round($distanceKm, 1),
            'radius_km'   => $setting->radius_km,
            'message'     => $available
                ? 'Great news — we deliver to your area!'
                : "Sorry, that's outside our {$setting->radius_km}km delivery area.",
        ]);
    }

    /**
     * @return array{lat: float, lng: float}|null
     */
    protected function geocodePostcode(string $postcode): ?array
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address'    => $postcode,
            'components' => 'country:GB', // keep results UK-only
            'key'        => config('services.google_maps.key'),
        ]);

        if (! $response->ok()) {
            return null;
        }

        $result = $response->json('results.0');

        if (! $result) {
            return null;
        }

        $location = $result['geometry']['location'] ?? null;

        if (! $location) {
            return null;
        }

        return [
            'lat' => (float) $location['lat'],
            'lng' => (float) $location['lng'],
        ];
    }
}