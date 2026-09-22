<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * IP -> country / region / city / approximate postcode using a free IP-lookup
 * HTTP API through Laravel's built-in Http client. No Composer package needed.
 *
 *  - Each IP is looked up ONCE and cached (30 days by default), so repeat visitors cost nothing.
 *  - The call happens inside the middleware's terminate() phase, i.e. after the page
 *    has already been sent, so it never slows a page down.
 *  - Failures are cached briefly (10 min) so an outage can't hammer the API.
 *  - The raw IP is never stored: cache keys are HMAC hashes.
 *
 * Drivers (config/tracking.php -> geo.driver / GEO_DRIVER in .env):
 *   ipwho  (default) https://ipwho.is   — no key needed
 *   ipinfo           https://ipinfo.io  — needs GEO_TOKEN (free tier available)
 */
class GeoLocator
{
    public static function lookup(?string $ip): array
    {
        if (! $ip || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return [];
        }

        $key    = 'geo:' . hash_hmac('sha256', $ip, (string) config('app.key'));
        $cached = Cache::get($key);

        if ($cached !== null) {
            return $cached; // [] is a cached "unknown", so it isn't retried every request
        }

        try {
            $geo = match (config('tracking.geo.driver')) {
                'ipinfo' => self::ipinfo($ip),
                default  => self::ipwho($ip),
            };
        } catch (\Throwable $e) {
            $geo = [];
        }

        Cache::put(
            $key,
            $geo,
            $geo ? now()->addDays((int) config('tracking.geo.cache_days')) : now()->addMinutes(10)
        );

        return $geo;
    }

    private static function ipwho(string $ip): array
    {
        $j = Http::timeout((int) config('tracking.geo.timeout'))
            ->acceptJson()
            ->get('https://ipwho.is/' . $ip)
            ->json();

        if (! is_array($j) || ! ($j['success'] ?? false) || empty($j['country_code'])) {
            return [];
        }

        return self::shape(
            $j['country_code'],
            $j['country'] ?? null,
            $j['region'] ?? null,
            $j['city'] ?? null,
            $j['postal'] ?? null,
            $j['latitude'] ?? null,
            $j['longitude'] ?? null
        );
    }

    private static function ipinfo(string $ip): array
    {
        $token = config('tracking.geo.token');

        if (! $token) {
            return [];
        }

        $j = Http::timeout((int) config('tracking.geo.timeout'))
            ->acceptJson()
            ->get("https://ipinfo.io/{$ip}/json", ['token' => $token])
            ->json();

        if (! is_array($j) || empty($j['country'])) {
            return [];
        }

        [$lat, $lng] = array_pad(explode(',', (string) ($j['loc'] ?? '')), 2, null);

        // ipinfo returns only the ISO code; derive the English country name when the intl extension exists.
        $name = class_exists(\Locale::class) ? \Locale::getDisplayRegion('-' . $j['country'], 'en') : $j['country'];

        return self::shape($j['country'], $name, $j['region'] ?? null, $j['city'] ?? null, $j['postal'] ?? null, $lat, $lng);
    }

    private static function shape($code, $country, $region, $city, $postal, $lat, $lng): array
    {
        return [
            'country_code'    => strtoupper((string) $code),
            'country'         => $country ?: null,
            'region'          => $region ?: null,
            'city'            => $city ?: null,
            'postal_code'     => $postal ? mb_substr((string) $postal, 0, 16) : null,
            // City-level accuracy at best, so keep 2 decimals (~1 km).
            'lat'             => is_numeric($lat) ? round((float) $lat, 2) : null,
            'lng'             => is_numeric($lng) ? round((float) $lng, 2) : null,
            'location_source' => 'ip',
        ];
    }
}
