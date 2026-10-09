<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Book a Table | {{ config('app.name', 'Restaurant') }}</title>
    <link rel="shortcut icon" href="{{ asset('storefront/assets/images/favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @php
        // Same Setting model the Contact page reads from — keeps the address in one place.
        $bookingSettings = \App\Models\Setting::current();
        $restaurantName  = config('app.name', 'Restaurant');
        $restaurantAddr  = $bookingSettings->address ?? null;
    @endphp

    <style>
        :root {
            --wine: #6C2233;
            --wine-dark: #571A28;
            --wine-pale: #E9BFC8;
            --page-bg: #F2EFE9;
            --card-bg: #FFFFFF;
            --line: #E7E1D9;
            --ink: #241A1D;
            --muted: #948B85;
            --error: #B3352C;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--page-bg);
            font-family: 'Inter', system-ui, sans-serif;
            color: var(--ink);
            -webkit-font-smoothing: antialiased;
        }

        .bw-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 20px;
        }

        .bw-card {
            width: 100%;
            max-width: 720px;
            background: var(--card-bg);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 24px 60px -20px rgba(87, 26, 40, .25);
        }

        /* ---------- Header ---------- */
        .bw-header {
            background: var(--wine);
            color: #fff;
            padding: 36px 40px 28px;
        }
        .bw-header h1 {
            font-family: 'Playfair Display', serif;
            font-weight: 800;
            font-size: 32px;
            margin: 0 0 10px;
            line-height: 1.15;
        }
        .bw-header .bw-addr {
            color: var(--wine-pale);
            font-size: 15px;
            margin: 0;
        }

        /* ---------- Step indicator ---------- */
        .bw-steps {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            padding: 24px 24px 4px;
        }
        .bw-step {
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--muted);
            white-space: nowrap;
        }
        .bw-step.is-active { color: var(--wine); }
        .bw-step.is-done { color: var(--ink); }
        .bw-step-line {
            width: 32px;
            height: 1px;
            background: var(--line);
        }

        /* ---------- Body ---------- */
        .bw-body { padding: 28px 40px 40px; }
        .bw-screen { display: none; }
        .bw-screen.is-active { display: block; }

        .bw-label {
            display: block;
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--ink);
            margin-bottom: 10px;
        }
        .bw-field { margin-bottom: 22px; }
        .bw-row { display: flex; gap: 20px; }
        .bw-row .bw-field { flex: 1; }

        .bw-input {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 15px;
            font-family: inherit;
            color: var(--ink);
            background: #fff;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .bw-input:focus {
            outline: none;
            border-color: var(--wine);
            box-shadow: 0 0 0 3px rgba(108, 34, 51, .12);
        }
        .bw-input.has-error { border-color: var(--error); }
        .bw-error {
            display: none;
            color: var(--error);
            font-size: 13px;
            margin-top: 6px;
        }
        .bw-error.show { display: block; }

        /* ---------- Party size stepper ---------- */
        .bw-stepper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 28px;
            padding: 20px 0 10px;
        }
        .bw-stepper button {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 1px solid var(--line);
            background: #fff;
            font-size: 20px;
            color: var(--wine);
            cursor: pointer;
            transition: background .15s ease, border-color .15s ease;
        }
        .bw-stepper button:hover:not(:disabled) { background: #FAF3F4; border-color: var(--wine); }
        .bw-stepper button:disabled { opacity: .35; cursor: not-allowed; }
        .bw-stepper-value { min-width: 120px; text-align: center; }
        .bw-stepper-value .n { display: block; font-family: 'Playfair Display', serif; font-size: 44px; font-weight: 800; line-height: 1; }
        .bw-stepper-value .label { display: block; font-size: 13px; color: var(--muted); margin-top: 6px; }
        .bw-party-hint { text-align: center; font-size: 13px; color: var(--muted); margin-top: 4px; }
        .bw-hours-hint { font-size: 13px; color: var(--muted); margin: -14px 0 22px; }
        .bw-hours-hint.is-closed { color: var(--error); font-weight: 600; }

        /* ---------- Event choice screen ---------- */
        .bw-intro-copy { color: var(--muted); font-size: 14.5px; margin: 0 0 22px; }
        .bw-event-list { display: flex; flex-direction: column; gap: 12px; }
        .bw-event-option {
            display: flex;
            align-items: center;
            gap: 14px;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 14px 16px;
            cursor: pointer;
            transition: border-color .15s ease, background .15s ease;
            text-align: left;
        }
        .bw-event-option:hover { border-color: var(--wine); background: #FAF3F4; }
        .bw-event-thumb {
            width: 48px; height: 48px; border-radius: 10px; flex-shrink: 0;
            background: var(--wine-pale) center/cover no-repeat;
            display: flex; align-items: center; justify-content: center;
            color: var(--wine); font-size: 18px;
        }
        .bw-event-info h4 { margin: 0 0 2px; font-size: 15px; font-weight: 700; }
        .bw-event-info p { margin: 0; font-size: 12.5px; color: var(--muted); }

        /* ---------- Notes ---------- */
        .bw-textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 15px;
            font-family: inherit;
            color: var(--ink);
            resize: vertical;
            min-height: 90px;
        }
        .bw-textarea:focus { outline: none; border-color: var(--wine); box-shadow: 0 0 0 3px rgba(108, 34, 51, .12); }

        /* ---------- Footer actions ---------- */
        .bw-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 8px;
        }
        .bw-back {
            font-size: 14px;
            font-weight: 600;
            color: var(--muted);
            background: none;
            border: none;
            cursor: pointer;
            padding: 12px 4px;
        }
        .bw-back:hover { color: var(--ink); }
        .bw-next {
            background: var(--wine);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 14px 30px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s ease;
        }
        .bw-next:hover:not(:disabled) { background: var(--wine-dark); }
        .bw-next:disabled { opacity: .55; cursor: not-allowed; }

        .bw-summary-banner {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #FAF3F4;
            border: 1px solid var(--wine-pale);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 24px;
            font-size: 13.5px;
            color: var(--wine-dark);
        }

        /* ---------- Done screen ---------- */
        .bw-done { text-align: center; padding: 20px 0 4px; }
        .bw-done-icon {
            width: 64px; height: 64px; border-radius: 50%;
            background: #FAF3F4; color: var(--wine);
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; margin: 0 auto 18px;
        }
        .bw-done h2 { font-family: 'Playfair Display', serif; font-size: 24px; margin: 0 0 8px; }
        .bw-done p { color: var(--muted); font-size: 14.5px; margin: 0 0 24px; }
        .bw-done-card { border: 1px solid var(--line); border-radius: 12px; padding: 20px; text-align: left; }
        .bw-done-row { display: flex; justify-content: space-between; padding: 7px 0; border-bottom: 1px solid var(--line); font-size: 14px; }
        .bw-done-row:last-child { border-bottom: none; }
        .bw-done-row span:first-child { color: var(--muted); }
        .bw-done-row span:last-child { font-weight: 700; }
        .bw-status-pending { color: #A9711A; }
        .bw-status-confirmed { color: #237A4B; }
        .bw-home-link {
            display: inline-block; margin-top: 24px; font-size: 14px; font-weight: 700;
            color: var(--wine); text-decoration: none;
        }

        @media (max-width: 560px) {
            .bw-header { padding: 28px 24px 22px; }
            .bw-header h1 { font-size: 26px; }
            .bw-body { padding: 22px 24px 30px; }
            .bw-row { flex-direction: column; gap: 0; }
        }
    </style>
</head>
<body>

<div class="bw-wrap">
    <div class="bw-card">

        <div class="bw-header">
            <h1>{{ $restaurantName }}</h1>
            @if ($restaurantAddr)
                <p class="bw-addr">📍 {{ $restaurantAddr }}</p>
            @endif
        </div>

        {{-- Step indicator — only shown while inside the 3-step form --}}
        <div class="bw-steps" id="bw-step-indicator" style="display:none;">
            <span class="bw-step" data-step-label="1">1 · Your Details</span>
            <span class="bw-step-line"></span>
            <span class="bw-step" data-step-label="2">2 · Party Size</span>
            <span class="bw-step-line"></span>
            <span class="bw-step" data-step-label="3">3 · Date &amp; Time</span>
        </div>

        <div class="bw-body">

            {{-- ============ SCREEN 0: choose an event, or a regular booking ============ --}}
            @if ($events->isNotEmpty())
                <div class="bw-screen is-active" data-screen="events">
                    <p class="bw-intro-copy">Choose a special event, or book a regular table.</p>
                    <div class="bw-event-list">
                        @foreach ($events as $event)
                            <button type="button" class="bw-event-option"
                                data-event-slug="{{ $event->slug }}" data-event-name="{{ $event->name }}"
                                data-min="{{ $event->party_size_min }}" data-max="{{ $event->party_size_max }}"
                                data-starts="{{ $event->starts_on->toDateString() }}" data-ends="{{ $event->ends_on->toDateString() }}">
                                <span class="bw-event-thumb" @if ($event->imageUrl()) style="background-image:url('{{ $event->imageUrl() }}')" @endif>
                                    @unless ($event->imageUrl())<i class="fa fa-calendar"></i>@endunless
                                </span>
                                <span class="bw-event-info">
                                    <h4>{{ $event->name }}</h4>
                                    <p>{{ $event->starts_on->format('j M') }} – {{ $event->ends_on->format('j M') }}</p>
                                </span>
                            </button>
                        @endforeach

                        <button type="button" class="bw-event-option" data-event-slug="" data-event-name="" data-min="1" data-max="20">
                            <span class="bw-event-thumb"><i class="fa fa-cutlery"></i></span>
                            <span class="bw-event-info">
                                <h4>Regular Booking</h4>
                                <p>Any day, no event</p>
                            </span>
                        </button>
                    </div>
                </div>
            @endif

            {{-- ============ STEP 1: details ============ --}}
            <div class="bw-screen @if ($events->isEmpty()) is-active @endif" data-screen="step-1">
                <div class="bw-summary-banner" id="bw-event-banner" style="display:none;">
                    <span>🎉</span> <span id="bw-event-banner-text"></span>
                </div>

                <div class="bw-field">
                    <label class="bw-label">Name</label>
                    <input type="text" class="bw-input" id="bw-name" placeholder="Full name"
                        value="{{ trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')) }}">
                    <div class="bw-error" id="bw-name-error"></div>
                </div>

                <div class="bw-row">
                    <div class="bw-field">
                        <label class="bw-label">Email</label>
                        <input type="email" class="bw-input" id="bw-email" placeholder="you@example.com" value="{{ $customer->email ?? '' }}">
                        <div class="bw-error" id="bw-email-error"></div>
                    </div>
                    <div class="bw-field">
                        <label class="bw-label">Phone</label>
                        <input type="text" class="bw-input" id="bw-phone" placeholder="07123456789">
                        <div class="bw-error" id="bw-phone-error"></div>
                    </div>
                </div>

                <div class="bw-actions">
                    <span></span>
                    <button type="button" class="bw-next" data-next="step-2">Next</button>
                </div>
            </div>

            {{-- ============ STEP 2: party size ============ --}}
            <div class="bw-screen" data-screen="step-2">
                <label class="bw-label" style="text-align:center; display:block;">How many guests?</label>

                <div class="bw-stepper">
                    <button type="button" id="bw-party-minus" aria-label="Decrease">–</button>
                    <div class="bw-stepper-value">
                        <span class="n" id="bw-party-number">2</span>
                        <span class="label" id="bw-party-label">guests</span>
                    </div>
                    <button type="button" id="bw-party-plus" aria-label="Increase">+</button>
                </div>
                <p class="bw-party-hint" id="bw-party-hint"></p>

                <div class="bw-actions" style="margin-top:28px;">
                    <button type="button" class="bw-back" data-back="step-1">Back</button>
                    <button type="button" class="bw-next" data-next="step-3">Next</button>
                </div>
            </div>

            {{-- ============ STEP 3: date & time ============ --}}
            <div class="bw-screen" data-screen="step-3">
                <div class="bw-row">
                    <div class="bw-field">
                        <label class="bw-label">Date</label>
                        <input type="date" class="bw-input" id="bw-date">
                        <div class="bw-error" id="bw-date-error"></div>
                    </div>
                    <div class="bw-field">
                        <label class="bw-label">Time</label>
                        <input type="time" class="bw-input" id="bw-time">
                        <div class="bw-error" id="bw-time-error"></div>
                    </div>
                </div>
                <p class="bw-hours-hint" id="bw-hours-hint"></p>

                <div class="bw-field">
                    <label class="bw-label">Notes <span style="font-weight:400;text-transform:none;color:var(--muted);">(optional)</span></label>
                    <textarea class="bw-textarea" id="bw-notes" placeholder="Dietary needs, occasion, special requests…"></textarea>
                </div>

                <div class="bw-error" id="bw-submit-error" style="margin-bottom:12px;"></div>

                <div class="bw-actions">
                    <button type="button" class="bw-back" data-back="step-2">Back</button>
                    <button type="button" class="bw-next" id="bw-submit">Confirm Booking</button>
                </div>
            </div>

            {{-- ============ DONE ============ --}}
            <div class="bw-screen" data-screen="done">
                <div class="bw-done">
                    <div class="bw-done-icon"><i class="fa fa-check"></i></div>
                    <h2>Booking received!</h2>
                    <p id="bw-done-copy">We'll see you soon.</p>

                    <div class="bw-done-card">
                        <div class="bw-done-row"><span>Reference</span><span id="bw-done-ref"></span></div>
                        <div class="bw-done-row" id="bw-done-event-row" style="display:none;"><span>Event</span><span id="bw-done-event"></span></div>
                        <div class="bw-done-row"><span>Date</span><span id="bw-done-date"></span></div>
                        <div class="bw-done-row"><span>Time</span><span id="bw-done-time"></span></div>
                        <div class="bw-done-row"><span>Guests</span><span id="bw-done-guests"></span></div>
                        <div class="bw-done-row"><span>Status</span><span id="bw-done-status"></span></div>
                    </div>

                    <a href="{{ route('storefront.home') }}" class="bw-home-link">← Back to home</a>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
(function ($) {
    "use strict";

    const $screens = $('.bw-screen');
    const $stepIndicator = $('#bw-step-indicator');
    const $stepLabels = $('.bw-step');
    const todayStr = new Date().toISOString().slice(0, 10);

    // One key per page load — a duplicated/retried request for the SAME
    // submission reuses it, which is what stops it creating a second GuestPlan
    // reservation (see BookingService::createBooking's idempotency check).
    const idempotencyKey = (window.crypto && crypto.randomUUID) ? crypto.randomUUID()
        : 'bw-' + Date.now() + '-' + Math.random().toString(36).slice(2);

    let selectedEventSlug = '';
    let selectedEventName = '';
    let partyMin = 1, partyMax = 20;
    let partyValue = 2;

    // Opening hours, keyed by weekday (0=Sun…6=Sat) — same source the admin
    // "Opening Hours" page writes to. See BookingController@index.
    const businessHours = @json($hours);
    const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    function hoursForDate(dateStr) {
        if (!dateStr) return null;
        const dow = new Date(dateStr + 'T00:00:00').getDay();
        return businessHours.find(h => h.day === dow) || null;
    }

    function applyHoursForSelectedDate() {
        const dateStr = $('#bw-date').val();
        const $time = $('#bw-time');
        const $hint = $('#bw-hours-hint');
        const h = hoursForDate(dateStr);

        if (!dateStr || !h) { $hint.text('').removeClass('is-closed'); $time.prop('disabled', false); return; }

        if (h.closed || !h.opens || !h.closes) {
            $hint.text('Closed on ' + dayNames[h.day] + 's — please choose another date.').addClass('is-closed');
            $time.prop('disabled', true).val('');
        } else {
            $time.prop('disabled', false);
            $time.attr('min', h.opens);
            if (!h.closesNextDay) $time.attr('max', h.closes); else $time.removeAttr('max');
            $hint.text('Open ' + formatTime(h.opens) + ' – ' + formatTime(h.closes) + (h.closesNextDay ? ' (past midnight)' : '') + ' on ' + dayNames[h.day] + 's.').removeClass('is-closed');
        }
    }

    function formatTime(hhmm) {
        const [h, m] = hhmm.split(':').map(Number);
        const period = h >= 12 ? 'PM' : 'AM';
        const h12 = ((h + 11) % 12) + 1;
        return `${h12}:${String(m).padStart(2, '0')} ${period}`;
    }

    $(document).on('change', '#bw-date', applyHoursForSelectedDate);

    function showScreen(name) {
        $screens.removeClass('is-active').filter(`[data-screen="${name}"]`).addClass('is-active');
        $stepIndicator.toggle(['step-1', 'step-2', 'step-3'].includes(name));
        updateStepIndicator(name);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function updateStepIndicator(current) {
        const order = ['step-1', 'step-2', 'step-3'];
        const idx = order.indexOf(current);
        $stepLabels.each(function (i) {
            $(this).removeClass('is-active is-done');
            if (i === idx) $(this).addClass('is-active');
            else if (i < idx) $(this).addClass('is-done');
        });
    }

    /* ---- Screen 0: event choice ---- */
    $('.bw-event-option').on('click', function () {
        const $el = $(this);
        selectedEventSlug = $el.data('event-slug') || '';
        selectedEventName = $el.data('event-name') || '';
        partyMin = parseInt($el.data('min'), 10) || 1;
        partyMax = parseInt($el.data('max'), 10) || 20;
        partyValue = Math.min(Math.max(2, partyMin), partyMax);

        const $dateInput = $('#bw-date');
        if (selectedEventSlug) {
            const starts = $el.data('starts'), ends = $el.data('ends');
            $dateInput.attr('min', starts && starts > todayStr ? starts : todayStr);
            if (ends) $dateInput.attr('max', ends); else $dateInput.removeAttr('max');
            $('#bw-event-banner-text').text('Booking for: ' + selectedEventName);
            $('#bw-event-banner').show();
        } else {
            $dateInput.attr('min', todayStr).removeAttr('max');
            $('#bw-event-banner').hide();
        }

        renderPartyStepper();
        applyHoursForSelectedDate();
        showScreen('step-1');
    });

    if ($('[data-screen="events"]').length === 0) {
        // No events at all — plain form only, defaults already in range
        $('#bw-date').attr('min', todayStr);
        renderPartyStepper();
        applyHoursForSelectedDate();
    }

    /* ---- Step navigation ---- */
    $('[data-next]').on('click', function () {
        const target = $(this).data('next');
        if (target === 'step-2' && !validateStep1()) return;
        showScreen(target);
    });
    $('[data-back]').on('click', function () {
        showScreen($(this).data('back'));
    });

    /* ---- Step 1 validation ---- */
    function clearError(field) {
        $('#' + field).removeClass('has-error');
        $('#' + field + '-error').removeClass('show').text('');
    }
    function setError(field, message) {
        $('#' + field).addClass('has-error');
        $('#' + field + '-error').addClass('show').text(message);
    }

    function validateStep1() {
        ['bw-name', 'bw-email', 'bw-phone'].forEach(clearError);
        let ok = true;

        if ($('#bw-name').val().trim() === '') { setError('bw-name', 'Please enter your name.'); ok = false; }

        const email = $('#bw-email').val().trim();
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { setError('bw-email', 'Please enter a valid email.'); ok = false; }

        const phone = $('#bw-phone').val().trim();
        if (!/^0\d{10}$/.test(phone)) { setError('bw-phone', 'Please enter a valid phone number, e.g. 07123456789.'); ok = false; }

        return ok;
    }

    /* ---- Step 2: party size stepper ---- */
    function renderPartyStepper() {
        $('#bw-party-number').text(partyValue);
        $('#bw-party-label').text(partyValue === 1 ? 'guest' : 'guests');
        $('#bw-party-hint').text(`Between ${partyMin} and ${partyMax} guests`);
        $('#bw-party-minus').prop('disabled', partyValue <= partyMin);
        $('#bw-party-plus').prop('disabled', partyValue >= partyMax);
    }
    $('#bw-party-minus').on('click', function () { if (partyValue > partyMin) { partyValue--; renderPartyStepper(); } });
    $('#bw-party-plus').on('click', function () { if (partyValue < partyMax) { partyValue++; renderPartyStepper(); } });

    /* ---- Step 3: submit ---- */
    $('#bw-submit').on('click', function () {
        ['bw-date', 'bw-time'].forEach(clearError);
        $('#bw-submit-error').removeClass('show').text('');
        let ok = true;

        const date = $('#bw-date').val();
        const time = $('#bw-time').val();
        if (!date) { setError('bw-date', 'Please choose a date.'); ok = false; }
        if (!time) { setError('bw-time', 'Please choose a time.'); ok = false; }

        const h = hoursForDate(date);
        if (date && h && (h.closed || !h.opens || !h.closes)) {
            setError('bw-date', "We're closed that day — please choose another date.");
            ok = false;
        }

        if (!ok) return;

        const $btn = $(this);
        $btn.prop('disabled', true).text('Booking…');

        fetch('/api/bookings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Idempotency-Key': idempotencyKey
            },
            body: JSON.stringify({
                event_slug: selectedEventSlug || null,
                customer_name: $('#bw-name').val().trim(),
                customer_email: $('#bw-email').val().trim(),
                customer_phone: $('#bw-phone').val().trim(),
                booking_date: date,
                booking_time: time,
                party_size: partyValue,
                notes: $('#bw-notes').val().trim() || null,
                idempotency_key: idempotencyKey,
            })
        })
        .then(res => res.json().then(payload => ({ ok: res.ok, payload })))
        .then(({ ok, payload }) => {
            if (!ok || !payload.success) {
                throw new Error((payload.error && payload.error.message) || 'Something went wrong.');
            }

            const b = payload.data;
            $('#bw-done-ref').text(b.bookingNumber || '—');
            if (b.eventName) {
                $('#bw-done-event').text(b.eventName);
                $('#bw-done-event-row').show();
            }
            $('#bw-done-date').text(new Date(b.bookingDate + 'T00:00:00').toLocaleDateString(undefined, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }));
            $('#bw-done-time').text(b.bookingTime);
            $('#bw-done-guests').text(b.partySize + (b.partySize === 1 ? ' guest' : ' guests'));

            const status = b.status || 'pending';
            $('#bw-done-status').text(status.charAt(0).toUpperCase() + status.slice(1)).attr('class', 'bw-status-' + status);
            $('#bw-done-copy').text(status === 'confirmed'
                ? "Your table is confirmed. We've sent a confirmation to your email."
                : status === 'failed'
                    ? "We've saved your request, but couldn't confirm it automatically — we'll call or email you to confirm."
                    : "We've got your request — we'll confirm shortly by email or phone.");

            showScreen('done');
        })
        .catch(err => {
            $('#bw-submit-error').addClass('show').text(err.message);
        })
        .finally(() => {
            $btn.prop('disabled', false).text('Confirm Booking');
        });
    });

})(jQuery);
</script>
</body>
</html>
