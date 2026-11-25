{{--
    Save as: resources/views/livewire/orders/partials/queue-card.blade.php

    One order card, used by every column of the accept-queue board.

    Expects:
      $order  the Order (with its items loaded)
      $stage  'pending' | 'confirmed' | 'preparing' | 'ready'

    This is @included inside the Livewire component's own view, so the
    wire:click / wire:model bindings below talk to AcceptQueue, and
    $adjustingOrderId / $adjustQuantities are its public properties.
    printOrder() comes from the x-data on the board's root; thermal printing
    calls window.printOrderReceipt() (admin layout) and shows a toast.
--}}
@php
    $typeLabel   = $order->order_type === 'dining' ? 'Dining' : ucfirst($order->order_type);
    $printUrl    = route('orders.print', ['order' => $order->id]);
    $isAdjusting = $stage === 'pending' && $adjustingOrderId === $order->id;
@endphp

<div class="accept-order-card" wire:key="{{ $stage }}-card-{{ $order->id }}">

    <div class="card-head">
        <span class="order-number">{{ $order->order_number }}</span>
        <span class="order-type type-{{ $order->order_type }}">{{ $typeLabel }}</span>
    </div>

    <div class="card-body">

        <div class="waiting-time">
            @if ($stage === 'pending')
                Placed {{ $order->created_at->diffForHumans() }}
            @elseif ($stage === 'confirmed')
                Confirmed {{ $order->updated_at->diffForHumans() }}
            @elseif ($stage === 'preparing')
                Preparing since {{ $order->updated_at->diffForHumans() }}
            @else
                Ready {{ $order->updated_at->diffForHumans() }}
            @endif
        </div>

        @if ($order->is_pre_order && $order->pre_order_date)
            <div class="preorder-flag">
                {{ $order->order_type === 'dining' ? 'Table for' : 'Scheduled' }}
                {{ \Illuminate\Support\Carbon::parse($order->pre_order_date)->format('D j M') }}@if ($order->pre_order_time), {{ \Illuminate\Support\Carbon::parse($order->pre_order_time)->format('g:i A') }}@endif
            </div>
        @endif

        <div class="customer-line">
            <strong>{{ $order->first_name }} {{ $order->last_name }}</strong> &middot; {{ $order->phone }}
            @if ($order->order_type === 'delivery')
                <br>{{ $order->address }}@if ($order->postcode), {{ $order->postcode }}@endif
            @endif
        </div>

        @if ($isAdjusting)

            {{-- ADJUST MODE (new orders only) --}}
            <div class="adjust-hint">Set quantity to 0 to remove an item.</div>

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
                    >&times;</button>
                </div>
            @endforeach

            @if ($order->order_type === 'delivery' && $order->delivery_fee > 0)
                <div class="adjust-hint mt-2 mb-0">
                    Delivery fee of £{{ number_format($order->delivery_fee, 2) }} stays on this order.
                </div>
            @endif

        @else

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

    {{-- ACTIONS — depend on the column the card is in --}}
    <div class="card-actions">

        @if ($stage === 'pending' && $isAdjusting)

            <button type="button" class="btn-cancel-adjust" wire:click="cancelAdjusting" wire:loading.attr="disabled">Cancel</button>
            <button type="button" class="btn-save-adjust" wire:click="saveAdjustment({{ $order->id }})" wire:loading.attr="disabled">Save Changes</button>

        @elseif ($stage === 'pending')

            <button
                type="button"
                class="btn-reject"
                wire:click="reject({{ $order->id }})"
                wire:confirm="Reject order {{ $order->order_number }}?"
                wire:loading.attr="disabled"
            >Reject</button>
            <button
                type="button"
                class="btn-adjust"
                wire:click="startAdjusting({{ $order->id }})"
                wire:loading.attr="disabled"
                title="Change quantities or remove an item before accepting"
            >Adjust</button>
            <button type="button" class="btn-accept" wire:click="accept({{ $order->id }})" wire:loading.attr="disabled">Accept</button>

        @elseif ($stage === 'confirmed')

            <button type="button" class="btn-prepare" wire:click="startPreparing({{ $order->id }})" wire:loading.attr="disabled">Start Preparing</button>

        @elseif ($stage === 'preparing')

            <button type="button" class="btn-ready" wire:click="markReady({{ $order->id }})" wire:loading.attr="disabled">Mark Ready</button>

        @else

            <button type="button" class="btn-complete" wire:click="complete({{ $order->id }})" wire:loading.attr="disabled">
                @switch($order->order_type)
                    @case('pickup') Picked Up @break
                    @case('dining') Served @break
                    @default Handed to Driver @break
                @endswitch
            </button>

        @endif

    </div>

    {{-- PRINT — available at every stage.
         Receipt = AJAX print to the Star printer (toast shows the result).
         A4 = printable page in a new tab. --}}
    <div class="card-print">
        <button type="button" x-on:click="printOrder({{ $order->id }}, '{{ $printUrl }}', 'thermal', $el)">Print receipt</button>
        <button type="button" x-on:click="printOrder({{ $order->id }}, '{{ $printUrl }}', 'a4', $el)">Print A4</button>
    </div>

</div>