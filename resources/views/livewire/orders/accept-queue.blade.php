{{-- resources/views/livewire/orders/accept-queue.blade.php --}}
<div
    wire:poll.3s.keep-alive="$refresh"
    x-data="{
        thermalWidth: localStorage.getItem('thermalWidth') || '80',

        /* Thermal = AJAX print straight to the Star printer (toast shows the result).
           A4 = printable page opened in a new tab. */
        printOrder(id, url, format, el) {
            if (format === 'thermal') {
                window.printOrderReceipt(id, el, this.thermalWidth);
                return;
            }
            window.open(url + '?format=a4', '_blank');
        },

        /* ---------- Uber-style looping order bell ---------- */
        wanted: false,      // server says: at least one live order needs action
        ringing: false,     // drives the shaking bell icon
        needsTap: false,    // browser blocked sound until the page is tapped once
        muted: localStorage.getItem('orderMuted') === '1',
        baseTitle: document.title,

        setRinging(on) {
            const was = this.wanted;
            this.wanted = on;
            if (on && !was && navigator.vibrate) navigator.vibrate([400, 200, 400]);
            this.sync();
        },
        sync() {
            const a = this.$refs.ring;
            const shouldRing = this.wanted && !this.muted;
            this.ringing = this.wanted;
            document.title = this.wanted ? '🔔 NEW ORDER!' : this.baseTitle;
            if (shouldRing) {
                if (a.paused) {
                    a.play().then(() => this.needsTap = false).catch(() => this.needsTap = true);
                }
            } else {
                a.pause();
                a.currentTime = 0;
                this.needsTap = false;
            }
        },
        enableSound() { this.sync(); },
        toggleMute() {
            this.muted = !this.muted;
            localStorage.setItem('orderMuted', this.muted ? '1' : '0');
            this.sync();
        }
    }"
    x-on:orders-need-action.window="setRinging($event.detail.count > 0)"
>

    {{-- Looping bell: plays while a live order is waiting, stops when none are --}}
    <audio x-ref="ring" src="{{ asset('storefront/assets/audio/ring.wav') }}" loop preload="auto"></audio>

    {{-- Browsers block sound until the page has been tapped once --}}
    <div x-show="needsTap" x-cloak x-on:click="enableSound()"
         style="position:fixed;top:0;left:0;right:0;z-index:4000;background:#D9A441;color:#1A1A1A;text-align:center;padding:14px;font-weight:800;cursor:pointer;">
        🔔 Tap here to turn on the order bell
    </div>

    <style>
        [x-cloak] { display: none !important; }

        .accept-queue-page {
            background: #0f0f0f;
            min-height: 100vh;
            padding: 24px;
        }

        .accept-queue-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .accept-queue-head h1 {
            color: #fff;
            font-weight: 800;
            margin: 0;
        }

        .accept-queue-tools {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .receipt-width {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #9ca3af;
            font-size: 13px;
        }

        .receipt-width select {
            background: #1f1f1f;
            color: #fff;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 13px;
        }

        /* ---------- Bell icon + mute button ---------- */

        .ring-bell { font-size: 26px; display: inline-block; opacity: .5; }

        .ring-bell.is-ringing {
            opacity: 1;
            animation: bell-shake .6s ease-in-out infinite;
            transform-origin: 50% 10%;
        }

        @keyframes bell-shake {
            0%, 100% { transform: rotate(0); }
            20%      { transform: rotate(18deg); }
            40%      { transform: rotate(-16deg); }
            60%      { transform: rotate(12deg); }
            80%      { transform: rotate(-8deg); }
        }

        .mute-btn {
            background: #1f1f1f;
            color: #fff;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 13px;
            font-weight: 700;
        }

        /* =========================================================
           FULL-SCREEN "TAP ANYWHERE TO ACCEPT" OVERLAY
           A brand-new order takes over the whole screen until it's
           acted on, rather than quietly waiting in a column.
        ========================================================== */

        .accept-overlay {
            position: fixed;
            inset: 0;
            z-index: 3000;
            background: rgba(0, 0, 0, .45);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .accept-overlay-card {
            background: #1a9c5c;
            border-radius: 24px;
            width: 380px;
            max-width: 90vw;
            padding: 48px 28px;
            text-align: center;
            cursor: pointer;
            animation: accept-overlay-pop .25s ease, accept-overlay-pulse 1.4s ease-in-out .25s infinite;
        }

        @keyframes accept-overlay-pop {
            from { transform: scale(.9); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }

        @keyframes accept-overlay-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, .55); }
            50%      { box-shadow: 0 0 0 26px rgba(255, 255, 255, 0); }
        }

        .accept-overlay-count {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .2);
            color: #fff;
            font-size: 44px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }

        .accept-overlay-title { color: #fff; font-size: 24px; font-weight: 800; }
        .accept-overlay-order-number { color: rgba(255, 255, 255, .85); font-size: 15px; margin-top: 4px; }
        .accept-overlay-total { color: #fff; font-size: 18px; font-weight: 800; margin-top: 10px; }
        .accept-overlay-sub { color: rgba(255, 255, 255, .75); font-size: 14px; margin-top: 14px; }

        .accept-overlay-actions { display: flex; gap: 10px; margin-top: 26px; }

        .accept-overlay-actions button {
            flex: 1;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-weight: 800;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: .02em;
        }

        .accept-overlay-btn-reject { background: rgba(255, 255, 255, .15); color: #fff; }
        .accept-overlay-btn-accept { background: #fff; color: #1a9c5c; }

        /* =========================================================
           BOARD — New / Confirmed / Preparing / Ready side by side.
           4 columns on a wide screen, 2 on a tablet, 1 on a phone.
        ========================================================== */

        .accept-queue-board {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
            align-items: start;
        }

        @media (max-width: 1399px) {
            .accept-queue-board { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 799px) {
            .accept-queue-board { grid-template-columns: 1fr; }
        }

        .accept-queue-column-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            padding: 0 4px;
        }

        .accept-queue-column-head h2 {
            color: #fff;
            font-size: 15px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin: 0;
        }

        .accept-queue-column-head .count-badge {
            background: #262626;
            color: #d1d5db;
            font-size: 12px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
        }

        .accept-queue-column.is-pending   .count-badge { background: #D9A441; color: #1A1A1A; }
        .accept-queue-column.is-confirmed .count-badge { background: #6366f1; color: #fff; }
        .accept-queue-column.is-preparing .count-badge { background: #2563eb; color: #fff; }
        .accept-queue-column.is-ready     .count-badge { background: #1a9c5c; color: #fff; }

        .accept-queue-column-body { display: flex; flex-direction: column; gap: 16px; }

        .accept-queue-column-empty {
            color: #6b7280;
            text-align: center;
            padding: 40px 12px;
            font-size: 13.5px;
            border: 1px dashed #2a2a2a;
            border-radius: 12px;
        }

        /* =========================================================
           ORDER CARD
        ========================================================== */

        .accept-order-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,.3);
            animation: accept-card-in .3s ease;
        }

        @keyframes accept-card-in {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .accept-order-card .card-head {
            background: #1A1A1A;
            color: #fff;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .accept-order-card .card-head .order-number { font-weight: 800; font-size: 16px; }

        .accept-order-card .card-head .order-type {
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .accept-order-card .order-type.type-delivery { background: #D9A441; color: #1A1A1A; }
        .accept-order-card .order-type.type-pickup   { background: #60a5fa; color: #0b2545; }
        .accept-order-card .order-type.type-dining   { background: #c4b5fd; color: #2e1065; }

        .accept-order-card .card-body { padding: 18px 20px; }

        .accept-order-card .waiting-time { font-size: 12px; color: #9ca3af; margin-bottom: 8px; }

        .accept-order-card .preorder-flag {
            background: #fdf6e9;
            border: 1px solid #f0dca3;
            color: #7a5c12;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 12.5px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .accept-order-card .customer-line {
            font-size: 13px;
            color: #374151;
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .accept-order-card .customer-line strong { color: #111827; }

        .accept-order-card .item-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            margin-bottom: 8px;
            gap: 10px;
        }

        .accept-order-card .item-line .qty { font-weight: 700; margin-right: 6px; }

        .accept-order-card .card-subtotals {
            border-top: 1px solid #eee;
            padding-top: 12px;
            margin-top: 10px;
        }

        .accept-order-card .card-subtotals .sub-line {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .accept-order-card .card-total {
            display: flex;
            justify-content: space-between;
            font-weight: 800;
            font-size: 16px;
        }

        .accept-order-card .card-actions { display: flex; gap: 10px; padding: 0 20px 14px; }

        .accept-order-card .card-actions button {
            flex: 1;
            border: none;
            border-radius: 10px;
            padding: 16px;
            font-weight: 800;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: .02em;
            transition: transform .1s ease, opacity .15s ease;
        }

        .accept-order-card .card-actions button:active { transform: scale(.96); }
        .accept-order-card .card-actions button[disabled] { opacity: .5; }

        .accept-order-card .btn-reject        { background: #fbe2e5; color: #b22b40; }
        .accept-order-card .btn-accept        { background: #1a9c5c; color: #fff; }
        .accept-order-card .btn-adjust        { background: #eef2ff; color: #3730a3; flex: 0 0 auto; padding-left: 18px; padding-right: 18px; }
        .accept-order-card .btn-prepare       { background: #6366f1; color: #fff; }
        .accept-order-card .btn-ready         { background: #2563eb; color: #fff; }
        .accept-order-card .btn-complete      { background: #1a9c5c; color: #fff; }
        .accept-order-card .btn-save-adjust   { background: #1a9c5c; color: #fff; }
        .accept-order-card .btn-cancel-adjust { background: #f3f4f6; color: #374151; }

        /* Print row — on every card, in every column */
        .accept-order-card .card-print { display: flex; gap: 8px; padding: 0 20px 18px; }

        .accept-order-card .card-print button {
            flex: 1;
            background: #fff;
            border: 1px solid #d1d5db;
            color: #374151;
            border-radius: 8px;
            padding: 9px 8px;
            font-size: 12px;
            font-weight: 700;
            transition: background .15s ease, border-color .15s ease;
        }

        .accept-order-card .card-print button:hover { background: #f3f4f6; border-color: #9ca3af; }
        .accept-order-card .card-print button[disabled] { opacity: .6; cursor: wait; }

        /* =========================================================
           ADJUST MODE
        ========================================================== */

        .accept-order-card .adjust-item-line { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .accept-order-card .adjust-item-line .item-name { flex: 1; font-size: 14px; }

        .accept-order-card .adjust-qty-input {
            width: 56px;
            text-align: center;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            padding: 6px 4px;
            font-size: 14px;
            font-weight: 700;
        }

        .accept-order-card .adjust-remove-btn {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 8px;
            background: #fbe2e5;
            color: #b22b40;
            flex-shrink: 0;
            font-size: 16px;
            line-height: 1;
        }

        .accept-order-card .adjust-hint { font-size: 12px; color: #9ca3af; margin-bottom: 14px; }
    </style>

    {{-- =========================================================
         FULL-SCREEN ACCEPT OVERLAY — for orders that need acting on
         right now. Pre-orders scheduled for a future date are
         excluded (see AcceptQueue::render): they sit in the New
         column instead of blacking out the screen days early.
         The looping bell rings for exactly as long as this shows.
    ========================================================== --}}

    @if ($takeoverOrders->isNotEmpty())
        @php
            $nextOrder = $takeoverOrders->first();
            $nextLabel = $nextOrder->order_type === 'dining' ? 'Dining' : ucfirst($nextOrder->order_type);
        @endphp

        <div class="accept-overlay" wire:key="overlay-{{ $nextOrder->id }}">
            <div class="accept-overlay-card" wire:click="accept({{ $nextOrder->id }})">
                <div class="accept-overlay-count">{{ $takeoverOrders->count() }}</div>
                <div class="accept-overlay-title">New order{{ $takeoverOrders->count() > 1 ? 's' : '' }}</div>
                <div class="accept-overlay-order-number">
                    {{ $nextOrder->order_number }} &middot; {{ $nextLabel }}
                    @if ($nextOrder->is_pre_order && $nextOrder->pre_order_time)
                        &middot; for {{ \Illuminate\Support\Carbon::parse($nextOrder->pre_order_time)->format('g:i A') }}
                    @endif
                </div>
                <div class="accept-overlay-total">£{{ number_format($nextOrder->total, 2) }}</div>
                <div class="accept-overlay-sub">Tap anywhere to accept</div>

                <div class="accept-overlay-actions">
                    <button
                        type="button"
                        class="accept-overlay-btn-reject"
                        wire:click.stop="reject({{ $nextOrder->id }})"
                        wire:confirm="Reject order {{ $nextOrder->order_number }}?"
                        wire:loading.attr="disabled"
                    >
                        Reject
                    </button>
                    <button
                        type="button"
                        class="accept-overlay-btn-accept"
                        wire:click.stop="accept({{ $nextOrder->id }})"
                        wire:loading.attr="disabled"
                    >
                        Accept
                    </button>
                </div>
            </div>
        </div>
    @endif

    @php
        $columns = [
            ['stage' => 'pending',   'title' => 'New',       'orders' => $pendingOrders,   'empty' => "No new orders — they'll appear here automatically."],
            ['stage' => 'confirmed', 'title' => 'Confirmed', 'orders' => $confirmedOrders, 'empty' => 'No confirmed orders waiting.'],
            ['stage' => 'preparing', 'title' => 'Preparing', 'orders' => $preparingOrders, 'empty' => 'Nothing being prepared right now.'],
            ['stage' => 'ready',     'title' => 'Ready',     'orders' => $readyOrders,     'empty' => 'Nothing ready for pickup or dispatch.'],
        ];

        $activeTotal = $pendingOrders->count() + $confirmedOrders->count() + $preparingOrders->count() + $readyOrders->count();
    @endphp

    <div class="accept-queue-page">

        <div class="accept-queue-head">
            <h1>Orders ({{ $activeTotal }})</h1>

            <div class="accept-queue-tools">
                {{-- Bell: shakes while an order is waiting; button mutes the sound only --}}
                <span class="ring-bell" :class="{ 'is-ringing': ringing }">🔔</span>
                <button type="button" class="mute-btn" x-on:click="toggleMute()"
                        x-text="muted ? '🔇 Bell off' : '🔔 Bell on'"></button>

                <label class="receipt-width">
                    Receipt width
                    <select x-model="thermalWidth" x-on:change="localStorage.setItem('thermalWidth', thermalWidth)">
                        <option value="80">80 mm</option>
                        <option value="58">58 mm</option>
                    </select>
                </label>
            </div>
        </div>

        <div class="accept-queue-board">
            @foreach ($columns as $column)

                <div class="accept-queue-column is-{{ $column['stage'] }}" wire:key="column-{{ $column['stage'] }}">

                    <div class="accept-queue-column-head">
                        <h2>{{ $column['title'] }}</h2>
                        <span class="count-badge">{{ $column['orders']->count() }}</span>
                    </div>

                    <div class="accept-queue-column-body">
                        @forelse ($column['orders'] as $order)
                            @include('livewire.orders.partials.queue-card', [
                                'order' => $order,
                                'stage' => $column['stage'],
                            ])
                        @empty
                            <div class="accept-queue-column-empty">{{ $column['empty'] }}</div>
                        @endforelse
                    </div>

                </div>

            @endforeach
        </div>

    </div>

</div>