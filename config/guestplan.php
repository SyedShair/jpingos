<?php

return [
    // From .env — never commit these
    // Accepts either var name — GUESTPLAN_API_URL is the current one, GUESTPLAN_BASE_URL is kept for back-compat.
    'base_url'      => env('GUESTPLAN_API_URL', env('GUESTPLAN_BASE_URL')),
    'access_key'    => env('GUESTPLAN_ACCESS_KEY'),
    'restaurant_id' => env('GUESTPLAN_RESTAURANT_ID'),

    // How the access key is sent. Confirmed so far: GuestPlan wants a header
    // literally named "Authorization" (a probe with x-api-key returned
    // {"code":3001,"description":"No authorization header."}). What VALUE
    // format it wants inside that header (Bearer <key>, raw key, etc.) is
    // still being confirmed — change auth_scheme below once you know it,
    // no code edit needed.
    'auth_header' => env('GUESTPLAN_AUTH_HEADER', 'Authorization'),

    // One of: 'bearer' (Authorization: Bearer <key>), 'raw' (Authorization: <key>),
    // 'apikey' (Authorization: ApiKey <key>), 'token' (Authorization: Token <key>).
    'auth_scheme' => env('GUESTPLAN_AUTH_SCHEME', 'bearer'),

    // Confirmed endpoint shape: POST {base_url}/restaurants/{restaurant_id}/reservations
    'reservation_path' => env('GUESTPLAN_RESERVATION_PATH', '/restaurants/{restaurant_id}/reservations'),

    // Seconds to wait for GuestPlan before treating the request as failed.
    'timeout' => 8,

    // If GuestPlan is down or times out, still save the booking locally and let staff
    // push it to GuestPlan manually from the admin page, instead of losing the booking.
    'fail_open' => true,
];
