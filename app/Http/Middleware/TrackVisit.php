<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use App\Services\GeoLocator;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Logs one row per real storefront page view.
 *
 *  - handle():    decides eligibility and sets the persistent visitor cookie.
 *  - terminate(): writes the row AFTER the response has been sent, so tracking
 *                 never slows a page down. Any failure is swallowed/reported so
 *                 it can never break the storefront.
 *
 * Attach ONLY to public storefront routes (see routes/web.php), never to admin.
 */
class TrackVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        // Disabled, or already handled earlier in this request (e.g. registered globally AND on a route group).
        if (! config('tracking.enabled') || $request->attributes->has('tracking.uid')) {
            return $next($request);
        }

        $cookieName = config('tracking.cookie');
        $uid        = (string) $request->cookie($cookieName);
        $isNew      = ! Str::isUuid($uid);

        if ($isNew) {
            $uid = (string) Str::uuid();
        }

        $eligible = $this->eligible($request);

        $request->attributes->set('tracking.uid', $uid);
        $request->attributes->set('tracking.eligible', $eligible);

        $response = $next($request);

        // Only hand a cookie to real (non-bot) page views.
        if ($eligible && $isNew) {
            $response->headers->setCookie(cookie(
                $cookieName,
                $uid,
                config('tracking.cookie_minutes'),
                '/',
                null,
                null,
                true,   // httpOnly
                false,  // raw
                'lax'
            ));
        }

        return $response;
    }

    public function terminate(Request $request, Response $response): void
    {
        if (! $request->attributes->get('tracking.eligible')) {
            return;
        }

        // Only successful HTML pages count (no redirects, 404s, downloads, JSON).
        if (! $response->isSuccessful()
            || ! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return;
        }

        try {
            $this->record($request);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    // ------------------------------------------------------------------

    private function eligible(Request $request): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        foreach ((array) config('tracking.except') as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        // Background calls: quickview, cart widgets, Livewire, browser prefetch, fetch() for JSON
        if ($request->ajax()
            || $request->expectsJson()
            || $request->header('X-Livewire')
            || $request->prefetch()) {
            return false;
        }

        $ua = (string) $request->userAgent();
        if ($ua === '' || preg_match(config('tracking.bot_pattern'), $ua)) {
            return false;
        }

        if (config('tracking.skip_admins')
            && Auth::guard(config('tracking.admin_guard'))->check()) {
            return false;
        }

        return true;
    }

    private function record(Request $request): void
    {
        $uid  = $request->attributes->get('tracking.uid');
        $path = Str::limit('/' . ltrim($request->path(), '/'), 255, '');

        // Same visitor + same page within a few seconds = reload, not a new view.
        if (! Cache::add('track:' . $uid . ':' . md5($path), 1, (int) config('tracking.dedupe_seconds'))) {
            return;
        }

        $ua = (string) $request->userAgent();

        // Precise (browser-granted) location wins; otherwise fall back to the IP database.
        $geo = Cache::get('visitor_geo:' . $uid) ?: GeoLocator::lookup($request->ip());

        $host     = parse_url((string) $request->headers->get('referer'), PHP_URL_HOST);
        $host     = $host ? preg_replace('/^www\./', '', strtolower($host)) : null;
        $referrer = ($host && strcasecmp($host, preg_replace('/^www\./', '', $request->getHost())) !== 0)
            ? Str::limit($host, 190, '')
            : null;

        Visit::create(array_merge([
            'visitor_uid'   => $uid,
            'ip_address'    => $request->ip(),
            'customer_id'   => Auth::guard('customer')->id(),
            'path'          => $path,
            'route_name'    => $request->route()?->getName(),
            'referrer_host' => $referrer,
            'device_type'   => $this->device($ua),
            'browser'       => $this->browser($ua),
            'created_at'    => now(),
        ], $geo));
    }

    private function device(string $ua): string
    {
        if (preg_match('/ipad|tablet|kindle|silk|playbook/i', $ua)
            || (preg_match('/android/i', $ua) && ! preg_match('/mobile/i', $ua))) {
            return 'tablet';
        }

        if (preg_match('/mobi|iphone|ipod|android|blackberry|opera mini|iemobile/i', $ua)) {
            return 'mobile';
        }

        return 'desktop';
    }

    private function browser(string $ua): string
    {
        return match (true) {
            str_contains($ua, 'Edg')                                    => 'Edge',
            str_contains($ua, 'OPR') || str_contains($ua, 'Opera')      => 'Opera',
            str_contains($ua, 'SamsungBrowser')                         => 'Samsung',
            str_contains($ua, 'Firefox') || str_contains($ua, 'FxiOS')  => 'Firefox',
            str_contains($ua, 'Chrome') || str_contains($ua, 'CriOS')   => 'Chrome',
            str_contains($ua, 'Safari')                                 => 'Safari',
            default                                                     => 'Other',
        };
    }
}
