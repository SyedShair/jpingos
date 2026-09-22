<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Receives browser coordinates (only after the visitor clicked "Allow"),
 * reverse-geocodes them with Google to get country / city / neighbourhood,
 * and attaches the result to this visitor's recent visits + future visits.
 */
class VisitorLocationController extends Controller
{
    public function store(Request $request): Response
    {
        $uid = (string) $request->cookie(config('tracking.cookie'));
        $key = config('services.google.maps_key');

        if (! Str::isUuid($uid) || ! $key) {
            return response()->noContent();
        }

        $data = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        // ~110 m precision is plenty for "area" and avoids storing an exact position.
        $lat = round((float) $data['lat'], 3);
        $lng = round((float) $data['lng'], 3);

        $res = Http::timeout(5)->get('https://maps.googleapis.com/maps/api/geocode/json', [
            'latlng'   => "{$lat},{$lng}",
            'language' => 'en',
            'key'      => $key,
        ])->json();

        if (($res['status'] ?? null) !== 'OK') {
            Log::warning('Google reverse geocoding failed', [
                'status' => $res['status'] ?? 'no-response',
                'error'  => $res['error_message'] ?? null,
            ]);

            return response()->noContent();
        }

        // Pool the components of every result, then take the first match per type.
        $components = collect($res['results'])->flatMap(fn ($r) => $r['address_components'] ?? []);

        $find = function (array $types, string $field = 'long_name') use ($components) {
            foreach ($types as $type) {
                $hit = $components->first(fn ($c) => in_array($type, $c['types'] ?? [], true));
                if ($hit) {
                    return $hit[$field];
                }
            }

            return null;
        };

        $geo = [
            'country_code'    => $find(['country'], 'short_name'),
            'country'         => $find(['country']),
            'region'          => $find(['administrative_area_level_1']),
            'city'            => $find(['locality', 'postal_town', 'administrative_area_level_2']),
            'area'            => $find(['sublocality_level_1', 'sublocality', 'neighborhood', 'administrative_area_level_3']),
            'postal_code'     => null,
            'lat'             => $lat,
            'lng'             => $lng,
            'location_source' => 'browser',
        ];

        if (! $geo['country']) {
            return response()->noContent();
        }

        // Remember for this visitor's future page views…
        Cache::put('visitor_geo:' . $uid, $geo, now()->addDays((int) config('tracking.precise_cache_days')));

        // …and upgrade the visits they've just made in this session.
        Visit::where('visitor_uid', $uid)
            ->where('created_at', '>=', now()->subMinutes(30))
            ->update($geo);

        return response()->noContent();
    }
}
