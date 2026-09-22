<?php

return [

    // Master switch. Set TRACKING_ENABLED=false in .env to stop logging without removing code.
    'enabled' => env('TRACKING_ENABLED', true),

    // Don't count visits made by a logged-in admin (guard used by your /admin login).
    'skip_admins' => true,
    'admin_guard' => 'web',

    // Persistent anonymous visitor cookie (1 year). Not tied to login or session.
    'cookie'         => 'visitor_uid',
    'cookie_minutes' => 60 * 24 * 365,

    // Paths that are NEVER tracked (matched with $request->is()). Only matters if you register the
    // middleware globally on the `web` group; harmless, extra protection when using the route group.
    'except' => [
        'admin', 'admin/*',
        'livewire/*', 'livewire-*/*',
        'account/verify/*', 'account/reset-password/*',
        'visitor/location', 'delivery-check',
        'dish/*/quickview', 'deals/*/quickview',
        'up', 'storage/*', 'build/*',
    ],

    // Ignore the same visitor hitting the same path again within N seconds (double reloads).
    'dedupe_seconds' => 10,

    // IP -> location lookup (HTTP API, no Composer package). Results are cached per IP.
    //   driver: 'ipwho'  (https://ipwho.is, no key)   |   'ipinfo' (https://ipinfo.io, needs token)
    'geo' => [
        'driver'     => env('GEO_DRIVER', 'ipwho'),
        'token'      => env('GEO_TOKEN'),
        'timeout'    => 2,     // seconds
        'cache_days' => 30,
    ],

    // How long a visitor's browser-granted precise location is remembered (days).
    'precise_cache_days' => 30,

    // Old rows are deleted by `php artisan visits:prune` (scheduled daily).
    'retention_days' => 180,

    // User-agents matching this are treated as bots and never logged.
    'bot_pattern' => '/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|facebot|mediapartners|headless|lighthouse|pagespeed|pingdom|uptime|monitor|curl|wget|python-requests|python-urllib|httpclient|okhttp|go-http|java\/|libwww|scrapy|semrush|ahrefs|mj12|dotbot|petalbot|bytespider|gptbot|claudebot|ccbot|amazonbot|preview|validator|phantomjs|node-fetch|axios/i',
];
