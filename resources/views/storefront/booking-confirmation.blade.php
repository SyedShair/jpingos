<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed | {{ config('app.name', 'Restaurant') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root { --wine:#6C2233; --wine-pale:#E9BFC8; --page-bg:#F2EFE9; --card-bg:#fff; --line:#E7E1D9; --ink:#241A1D; --muted:#948B85; }
        * { box-sizing: border-box; }
        body { margin:0; background:var(--page-bg); font-family:'Inter',system-ui,sans-serif; color:var(--ink); }
        .bw-wrap { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:48px 20px; }
        .bw-card { width:100%; max-width:560px; background:var(--card-bg); border-radius:20px; overflow:hidden; box-shadow:0 24px 60px -20px rgba(87,26,40,.25); }
        .bw-header { background:var(--wine); color:#fff; padding:36px 40px 28px; }
        .bw-header h1 { font-family:'Playfair Display',serif; font-weight:800; font-size:28px; margin:0; }
        .bw-body { padding:32px 40px 40px; text-align:center; }
        .bw-done-icon { width:64px; height:64px; border-radius:50%; background:#FAF3F4; color:var(--wine); display:flex; align-items:center; justify-content:center; font-size:26px; margin:0 auto 18px; }
        .bw-body h2 { font-family:'Playfair Display',serif; font-size:24px; margin:0 0 8px; }
        .bw-body p { color:var(--muted); font-size:14.5px; margin:0 0 24px; }
        .bw-done-card { border:1px solid var(--line); border-radius:12px; padding:20px; text-align:left; }
        .bw-done-row { display:flex; justify-content:space-between; padding:7px 0; border-bottom:1px solid var(--line); font-size:14px; }
        .bw-done-row:last-child { border-bottom:none; }
        .bw-done-row span:first-child { color:var(--muted); }
        .bw-done-row span:last-child { font-weight:700; }
        .bw-status-pending { color:#A9711A; }
        .bw-status-confirmed { color:#237A4B; }
        .bw-home-link { display:inline-block; margin-top:24px; font-size:14px; font-weight:700; color:var(--wine); text-decoration:none; }
    </style>
</head>
<body>
    <div class="bw-wrap">
        <div class="bw-card">
            <div class="bw-header"><h1>{{ config('app.name', 'Restaurant') }}</h1></div>
            <div class="bw-body">
                <div class="bw-done-icon"><i class="fa fa-check"></i></div>
                <h2>Booking received!</h2>
                <p>
                    @if ($booking->status === 'confirmed')
                        Your table is confirmed. We've sent a confirmation to {{ $booking->customer_email }}.
                    @else
                        We've got your request — we'll confirm shortly by email or phone.
                    @endif
                </p>

                <div class="bw-done-card">
                    <div class="bw-done-row"><span>Reference</span><span>{{ $booking->booking_number }}</span></div>
                    @if ($booking->event)
                        <div class="bw-done-row"><span>Event</span><span>{{ $booking->event->name }}</span></div>
                    @endif
                    <div class="bw-done-row"><span>Date</span><span>{{ $booking->booking_date->format('l, j F Y') }}</span></div>
                    <div class="bw-done-row"><span>Time</span><span>{{ \Illuminate\Support\Carbon::parse($booking->booking_time)->format('g:i A') }}</span></div>
                    <div class="bw-done-row"><span>Party size</span><span>{{ $booking->party_size }} {{ Str::plural('guest', $booking->party_size) }}</span></div>
                    <div class="bw-done-row"><span>Status</span><span class="bw-status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span></div>
                </div>

                <a href="{{ route('storefront.home') }}" class="bw-home-link">← Back to home</a>
            </div>
        </div>
    </div>
</body>
</html>
