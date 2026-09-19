<div wire:poll.3s="$refresh" x-on:new-order-received.window="$refs.chime.play().catch(() => {})">

    <audio x-ref="chime" src="{{ asset('storefront/assets/audio/new-order.mp3') }}" preload="auto"></audio>

    <style>
        .accept-queue-page {
            background: #0f0f0f;
            min-height: 100vh;
            padding: 24px;
        }

        .accept-queue-page h1 {
            color: #fff;
            font-weight: 800;
            margin-bottom: 24px;
        }

        /* =========================================================
           FULL-SCREEN "TAP ANYWHERE TO ACCEPT" OVERLAY
           Mirrors the Uber Eats order-manager pattern: a brand-new
           order takes over the whole screen until it's acted on,
           rather than quietly waiting in a column for someone to
           notice it.
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
            animation: accept-overlay-pop .25s ease;
        }

        @keyframes accept-overlay-pop {
            from { transform: scale(.9); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
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

        .accept-overlay-title {
            color: #fff;
            font-size: 24px;
            font-weight: 800;
        }

        .accept-overlay-order-number {
            color: rgba(255, 255, 255, .85);
            font-size: 15px;
            margin-top: 4px;
        }

        .accept-overlay-total {
            color: #fff;
            font-size: 18px;
            font-weight: 800;
            margin-top: 10px;
        }

        .accept-overlay-sub {
            color: rgba(255, 255, 255, .75);
            font-size: 14px;
            margin-top: 14px;
        }

        .accept-overlay-actions {
            display: flex;
            gap: 10px;
            margin-top: 26px;
        }

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

        .accept-overlay-btn-reject {
            background: rgba(255, 255, 255, .15);
            color: #fff;
        }

        .accept-overlay-btn-accept {
            background: #fff;
            color: #1a9c5c;
        }

        /* =========================================================
           BOARD LAYOUT — three columns side by side, like an
           Uber-Eats-style order manager, rather than tabs. A busy
           kitchen needs New/Preparing/Ready all visible at once.
        ========================================================== */

        .accept-queue-board {
            display: grid;
            grid-template-columns: repeat(3, minmax(320px, 1fr));
            gap: 20px;
            align-items: start;
        }

        @media (max-width: 1100px) {
            .accept-queue-board {
                grid-template-columns: 1fr;
            }
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

        .accept-queue-column.is-new .accept-queue-column-head .count-badge {
            background: #D9A441;
            color: #1A1A1A;
        }

        .accept-queue-column.is-preparing .accept-queue-column-head .count-badge {
            background: #2563eb;
            color: #fff;
        }

        .accept-queue-column.is-ready .accept-queue-column-head .count-badge {
            background: #1a9c5c;
            color: #fff;
        }

        .accept-queue-column-body {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

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
            to { opacity: 1; transform: translateY(0); }
        }

        .accept-order-card .card-head {
            background: #1A1A1A;
            color: #fff;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .accept-order-card .card-head .order-number { font-weight: 800; font-size: 16px; }

        .accept-order-card .card-head .head-badges {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .accept-order-card .card-head .order-type {
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            padding: 4px 10px;
            border-radius: 20px;
        }

        /* One badge colour per fulfilment type, so a glance at the
           column tells you what kind of work each card is. */
        .accept-order-card .order-type.type-delivery { background: #D9A441; color: #1A1A1A; }
        .accept-order-card .order-type.type-pickup   { background: #60a5fa; color: #0b2545; }
        .accept-order-card .order-type.type-dining   { background: #c4b5fd; color: #2e1065; }

        /* Pre-order gets its own badge in the header so it's obvious
           before you even read the scheduled time below. */
        .accept-order-card .preorder-badge {
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            padding: 4px 10px;
            border-radius: 20px;
            background: #fde68a;
            color: #713f12;
        }

        .accept-order-card .card-body { padding: 18px 20px; }

        .accept-order-card .preorder-flag {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            background: #fdf6e9;
            border: 1px solid #f0dca3;
            color: #7a5c12;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 12.5px;
            font-weight: 700;
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .accept-order-card .preorder-flag .preorder-time {
            font-size: 14px;
            display: block;
        }

        .accept-order-card .preorder-flag .preorder-meta {
            font-weight: 600;
            opacity: .85;
            display: block;
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

        .accept-order-card .item-line .qty {
            font-weight: 700;
            margin-right: 6px;
        }

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

        .accept-order-card .card-actions {
            display: flex;
            gap: 10px;
            padding: 0 20px 20px;
        }

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

        .accept-order-card .btn-reject { background: #fbe2e5; color: #b22b40; }
        .accept-order-card .btn-accept { background: #1a9c5c; color: #fff; }
        .accept-order-card .btn-adjust { background: #eef2ff; color: #3730a3; flex: 0 0 auto; padding-left: 18px; padding-right: 18px; }
        .accept-order-card .btn-ready { background: #2563eb; color: #fff; }
        .accept-order-card .btn-complete { background: #1a9c5c; color: #fff; }
        .accept-order-card .btn-save-adjust { background: #1a9c5c; color: #fff; }
        .accept-order-card .btn-cancel-adjust { background: #f3f4f6; color: #374151; }

        .accept-order-card .waiting-time { font-size: 12px; color: #9ca3af; }

        /* =========================================================
           ADJUST MODE
        ========================================================== */

        .accept-order-card .adjust-item-line {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .accept-order-card .adjust-item-line .item-name {
            flex: 1;
            font-size: 14px;
        }

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
        }

        .accept-order-card .adjust-hint {
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 14px;
        }

    </style>

    {{-- =========================================================
         SHARED CARD HEADER
         order number + fulfilment type + pre-order badge
    ========================================================== --}}
    @php
        $typeLabel = fn ($type) => $type === 'dining' ? 'Dining' : ucfirst($type);
    @endphp

    {{-- =========================================================
         FULL-SCREEN ACCEPT OVERLAY — shown for orders that need
         acting on right now. Pre-orders scheduled for a future date
         are deliberately excluded (see AcceptQueue::render): they
         sit in the New column instead of blacking out the screen
         days early.
    ========================================================== --}}

    @if ($takeoverOrders->isNotEmpty())
        @php
            $nextOrder = $takeoverOrders->first();
            $nextType  = $nextOrder->order_type;
            $nextLabel = $typeLabel($nextType);
        @endphp

        <div class="accept-overlay" wire:key="overlay-{{ $nextOrder->id }}">
            <div class="accept-overlay-card" wire:click="accept({{ $nextOrder->id }})">
                <div class="accept-overlay-count">{{ $takeoverOrders->count() }}</div>
                <div class="accept-overlay-title">
                    New {{ $nextOrder->is_pre_order ? 'pre-order' : 'order' }}{{ $takeoverOrders->count() > 1 ? 's' : '' }}
                </div>
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

    <div class="accept-queue-page">
        <h1>
            Orders
            ({{ $pendingOrders->count() + $preparingOrders->count() + $readyOrders->count() }})
        </h1>

        <div class="accept-queue-board">

            {{-- =================================================
                 NEW ORDERS (pending) — accept / reject / adjust
            ================================================== --}}

            <div class="accept-queue-column is-new">

                <div class="accept-queue-column-head">
                    <h2>New</h2>
                    <span class="count-badge">{{ $pendingOrders->count() }}</span>
                </div>

                <div class="accept-queue-column-body">

                    @forelse ($pendingOrders as $order)

                        <div class="accept-order-card" wire:key="pending-card-{{ $order->id }}">

                            <div class="card-head">
                                <span class="order-number">{{ $order->order_number }}</span>
                                <span class="head-badges">
                                    @if ($order->is_pre_order)
                                        <span class="preorder-badge">Pre-order</span>
                                    @endif
                                    <span class="order-type type-{{ $order->order_type }}">
                                        {{ $typeLabel($order->order_type) }}
                                    </span>
                                </span>
                            </div>

                            <div class="card-body">
                                <div class="waiting-time mb-2">
                                    Placed {{ $order->created_at->diffForHumans() }}
                                </div>

                                @if ($order->is_pre_order && $order->pre_order_date)
                                    <div class="preorder-flag">
                                        <i class="fa fa-calendar mt-1"></i>
                                        <span>
                                            <span class="preorder-time">
                                                {{ \Illuminate\Support\Carbon::parse($order->pre_order_date)->format('D j M') }}@if ($order->pre_order_time) &middot; {{ \Illuminate\Support\Carbon::parse($order->pre_order_time)->format('g:i A') }}@endif
                                            </span>
                                            <span class="preorder-meta">
                                                {{ $order->order_type === 'dining' ? 'Table reservation' : $typeLabel($order->order_type) . ' pre-order' }}
                                            </span>
                                        </span>
                                    </div>
                                @endif

                                <div class="customer-line">
                                    <strong>{{ $order->first_name }} {{ $order->last_name }}</strong> &middot; {{ $order->phone }}
                                    @if ($order->order_type === 'delivery')
                                        <br>{{ $order->address }}@if ($order->postcode), {{ $order->postcode }}@endif
                                    @endif
                                </div>

                                @if ($adjustingOrderId === $order->id)

                                    {{-- =========================
                                         ADJUST MODE
                                    ========================== --}}

                                    <div class="adjust-hint">
                                        Set quantity to 0 to remove an item.
                                    </div>

                                    @foreach ($order->items as $item)
                                        <div class="adjust-item-line" wire:key="adjust-item-{{ $item->id }}">
                                            <span class="item-name">{{ $item->name }}</span>
                                            <input
                                                type="number"
                                                min="0"
                                                class="adjust-qty-input"
                                                wire:model="adjustQuantities.{{ $item->id }}"
                                            >
                                            <button
                                                type="button"
                                                class="adjust-remove-btn"
                                                wire:click="removeAdjustedItem({{ $item->id }})"
                                                title="Remove item"
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    @endforeach

                                    @if ($order->order_type === 'delivery' && $order->delivery_fee > 0)
                                        <div class="adjust-hint mt-2 mb-0">
                                            Delivery fee of £{{ number_format($order->delivery_fee, 2) }} stays on this order.
                                        </div>
                                    @endif

                                @else

                                    {{-- =========================
                                         NORMAL VIEW
                                    ========================== --}}

                                    @foreach ($order->items as $item)
                                        <div class="item-line">
                                            <span><span class="qty">{{ $item->quantity }}×</span>{{ $item->name }}</span>
                                            <span>£{{ number_format($item->line_total, 2) }}</span>
                                        </div>
                                    @endforeach

                                    <div class="card-subtotals">
                                        @if ($order->order_type === 'delivery')
                                            <div class="sub-line">
                                                <span>Subtotal</span>
                                                <span>£{{ number_format($order->subtotal, 2) }}</span>
                                            </div>
                                            <div class="sub-line">
                                                <span>Delivery fee</span>
                                                <span>{{ $order->delivery_fee > 0 ? '£' . number_format($order->delivery_fee, 2) : 'Free' }}</span>
                                            </div>
                                        @endif

                                        <div class="card-total">
                                            <span>Total</span>
                                            <span>£{{ number_format($order->total, 2) }}</span>
                                        </div>
                                    </div>

                                @endif

                            </div>

                            @if ($adjustingOrderId === $order->id)

                                <div class="card-actions">
                                    <button
                                        type="button"
                                        class="btn-cancel-adjust"
                                        wire:click="cancelAdjusting"
                                        wire:loading.attr="disabled"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="button"
                                        class="btn-save-adjust"
                                        wire:click="saveAdjustment({{ $order->id }})"
                                        wire:loading.attr="disabled"
                                    >
                                        Save Changes
                                    </button>
                                </div>

                            @else

                                <div class="card-actions">
                                    <button
                                        type="button"
                                        class="btn-reject"
                                        wire:click="reject({{ $order->id }})"
                                        wire:confirm="Reject order {{ $order->order_number }}?"
                                        wire:loading.attr="disabled"
                                    >
                                        Reject
                                    </button>
                                    <button
                                        type="button"
                                        class="btn-adjust"
                                        wire:click="startAdjusting({{ $order->id }})"
                                        wire:loading.attr="disabled"
                                        title="Change quantities or remove an item before accepting"
                                    >
                                        Adjust
                                    </button>
                                    <button
                                        type="button"
                                        class="btn-accept"
                                        wire:click="accept({{ $order->id }})"
                                        wire:loading.attr="disabled"
                                    >
                                        Accept
                                    </button>
                                </div>

                            @endif

                        </div>

                    @empty

                        <div class="accept-queue-column-empty">
                            No new orders — they'll appear here automatically.
                        </div>

                    @endforelse

                </div>

            </div>


            {{-- =================================================
                 PREPARING (confirmed) — mark ready
            ================================================== --}}

            <div class="accept-queue-column is-preparing">

                <div class="accept-queue-column-head">
                    <h2>Preparing</h2>
                    <span class="count-badge">{{ $preparingOrders->count() }}</span>
                </div>

                <div class="accept-queue-column-body">

                    @forelse ($preparingOrders as $order)

                        <div class="accept-order-card" wire:key="preparing-card-{{ $order->id }}">

                            <div class="card-head">
                                <span class="order-number">{{ $order->order_number }}</span>
                                <span class="head-badges">
                                    @if ($order->is_pre_order)
                                        <span class="preorder-badge">Pre-order</span>
                                    @endif
                                    <span class="order-type type-{{ $order->order_type }}">
                                        {{ $typeLabel($order->order_type) }}
                                    </span>
                                </span>
                            </div>

                            <div class="card-body">
                                <div class="waiting-time mb-2">
                                    Accepted {{ $order->updated_at->diffForHumans() }}
                                </div>

                                @if ($order->is_pre_order && $order->pre_order_date)
                                    <div class="preorder-flag">
                                        <i class="fa fa-calendar mt-1"></i>
                                        <span>
                                            <span class="preorder-time">
                                                {{ \Illuminate\Support\Carbon::parse($order->pre_order_date)->format('D j M') }}@if ($order->pre_order_time) &middot; {{ \Illuminate\Support\Carbon::parse($order->pre_order_time)->format('g:i A') }}@endif
                                            </span>
                                            <span class="preorder-meta">
                                                {{ $order->order_type === 'dining' ? 'Table reservation' : $typeLabel($order->order_type) . ' pre-order' }}
                                            </span>
                                        </span>
                                    </div>
                                @endif

                                @foreach ($order->items as $item)
                                    <div class="item-line">
                                        <span><span class="qty">{{ $item->quantity }}×</span>{{ $item->name }}</span>
                                        <span>£{{ number_format($item->line_total, 2) }}</span>
                                    </div>
                                @endforeach

                                <div class="card-subtotals">
                                    @if ($order->order_type === 'delivery')
                                        <div class="sub-line">
                                            <span>Subtotal</span>
                                            <span>£{{ number_format($order->subtotal, 2) }}</span>
                                        </div>
                                        <div class="sub-line">
                                            <span>Delivery fee</span>
                                            <span>{{ $order->delivery_fee > 0 ? '£' . number_format($order->delivery_fee, 2) : 'Free' }}</span>
                                        </div>
                                    @endif

                                    <div class="card-total">
                                        <span>Total</span>
                                        <span>£{{ number_format($order->total, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="card-actions">
                                <button
                                    type="button"
                                    class="btn-ready"
                                    wire:click="markReady({{ $order->id }})"
                                    wire:loading.attr="disabled"
                                >
                                    Mark Ready
                                </button>
                            </div>

                        </div>

                    @empty

                        <div class="accept-queue-column-empty">
                            Nothing in the kitchen right now.
                        </div>

                    @endforelse

                </div>

            </div>


            {{-- =================================================
                 READY — complete (picked up / handed to courier /
                 served at the table)
            ================================================== --}}

            <div class="accept-queue-column is-ready">

                <div class="accept-queue-column-head">
                    <h2>Ready</h2>
                    <span class="count-badge">{{ $readyOrders->count() }}</span>
                </div>

                <div class="accept-queue-column-body">

                    @forelse ($readyOrders as $order)

                        <div class="accept-order-card" wire:key="ready-card-{{ $order->id }}">

                            <div class="card-head">
                                <span class="order-number">{{ $order->order_number }}</span>
                                <span class="head-badges">
                                    @if ($order->is_pre_order)
                                        <span class="preorder-badge">Pre-order</span>
                                    @endif
                                    <span class="order-type type-{{ $order->order_type }}">
                                        {{ $typeLabel($order->order_type) }}
                                    </span>
                                </span>
                            </div>

                            <div class="card-body">
                                <div class="waiting-time mb-2">
                                    Ready {{ $order->updated_at->diffForHumans() }}
                                </div>

                                @if ($order->is_pre_order && $order->pre_order_date)
                                    <div class="preorder-flag">
                                        <i class="fa fa-calendar mt-1"></i>
                                        <span>
                                            <span class="preorder-time">
                                                {{ \Illuminate\Support\Carbon::parse($order->pre_order_date)->format('D j M') }}@if ($order->pre_order_time) &middot; {{ \Illuminate\Support\Carbon::parse($order->pre_order_time)->format('g:i A') }}@endif
                                            </span>
                                            <span class="preorder-meta">
                                                {{ $order->order_type === 'dining' ? 'Table reservation' : $typeLabel($order->order_type) . ' pre-order' }}
                                            </span>
                                        </span>
                                    </div>
                                @endif

                                <div class="customer-line">
                                    <strong>{{ $order->first_name }} {{ $order->last_name }}</strong> &middot; {{ $order->phone }}
                                    @if ($order->order_type === 'delivery')
                                        <br>{{ $order->address }}@if ($order->postcode), {{ $order->postcode }}@endif
                                    @endif
                                </div>

                                @foreach ($order->items as $item)
                                    <div class="item-line">
                                        <span><span class="qty">{{ $item->quantity }}×</span>{{ $item->name }}</span>
                                        <span>£{{ number_format($item->line_total, 2) }}</span>
                                    </div>
                                @endforeach

                                <div class="card-subtotals">
                                    @if ($order->order_type === 'delivery')
                                        <div class="sub-line">
                                            <span>Subtotal</span>
                                            <span>£{{ number_format($order->subtotal, 2) }}</span>
                                        </div>
                                        <div class="sub-line">
                                            <span>Delivery fee</span>
                                            <span>{{ $order->delivery_fee > 0 ? '£' . number_format($order->delivery_fee, 2) : 'Free' }}</span>
                                        </div>
                                    @endif

                                    <div class="card-total">
                                        <span>Total</span>
                                        <span>£{{ number_format($order->total, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="card-actions">
                                <button
                                    type="button"
                                    class="btn-complete"
                                    wire:click="complete({{ $order->id }})"
                                    wire:loading.attr="disabled"
                                >
                                    @switch($order->order_type)
                                        @case('pickup') Picked Up @break
                                        @case('dining') Served @break
                                        @default Handed to Courier
                                    @endswitch
                                </button>
                            </div>

                        </div>

                    @empty

                        <div class="accept-queue-column-empty">
                            Nothing ready for pickup/dispatch.
                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

</div>