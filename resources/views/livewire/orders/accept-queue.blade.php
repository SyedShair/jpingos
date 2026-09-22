<div wire:poll.3s="$refresh" x-data="{ prevCount: {{ $pendingOrders->count() }} }" x-init="
    $wire.on('$refresh', () => {
        const newCount = {{ $pendingOrders->count() }};
        if (newCount > prevCount) {
            $refs.chime.play().catch(() => {});
        }
        prevCount = newCount;
    });
">

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

        .accept-queue-empty {
            color: #6b7280;
            text-align: center;
            padding: 100px 0;
            font-size: 20px;
        }

        .accept-queue-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 20px;
        }

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
        }

        .accept-order-card .card-head .order-number { font-weight: 800; font-size: 16px; }
        .accept-order-card .card-head .order-type {
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            background: #D9A441;
            color: #1A1A1A;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .accept-order-card .card-body { padding: 18px 20px; }

        .accept-order-card .item-line {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .accept-order-card .item-line .qty {
            font-weight: 700;
            margin-right: 6px;
        }

        .accept-order-card .card-total {
            display: flex;
            justify-content: space-between;
            font-weight: 800;
            font-size: 16px;
            border-top: 1px solid #eee;
            padding-top: 12px;
            margin-top: 10px;
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

        .accept-order-card .waiting-time { font-size: 12px; color: #9ca3af; }
    </style>

    <div class="accept-queue-page">
        <h1>Incoming Orders ({{ $pendingOrders->count() }})</h1>

        @if ($pendingOrders->isEmpty())

            <div class="accept-queue-empty">
                No pending orders — new orders will appear here automatically.
            </div>

        @else

            <div class="accept-queue-grid">
                @foreach ($pendingOrders as $order)
                    <div class="accept-order-card" wire:key="accept-card-{{ $order->id }}">

                        <div class="card-head">
                            <span class="order-number">{{ $order->order_number }}</span>
                            <span class="order-type">{{ $order->order_type }}</span>
                        </div>

                        <div class="card-body">
                            <div class="waiting-time mb-2">
                                Placed {{ $order->created_at->diffForHumans() }}
                            </div>

                            @foreach ($order->items as $item)
                                <div class="item-line">
                                    <span><span class="qty">{{ $item->quantity }}×</span>{{ $item->name }}</span>
                                    <span>£{{ number_format($item->line_total, 2) }}</span>
                                </div>
                            @endforeach

                            <div class="card-total">
                                <span>Total</span>
                                <span>£{{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>

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
                                class="btn-accept"
                                wire:click="accept({{ $order->id }})"
                                wire:loading.attr="disabled"
                            >
                                Accept
                            </button>
                        </div>

                    </div>
                @endforeach
            </div>

        @endif
    </div>

</div>