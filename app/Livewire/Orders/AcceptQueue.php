<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Livewire\Component;

class AcceptQueue extends Component
{
    /*
    |--------------------------------------------------------------------------
    | Order Adjustment State
    |
    | "Adjust" isn't a status — it's a mode this component enters for one
    | order at a time, before that order is accepted. It lets the kitchen
    | drop an out-of-stock item or correct a quantity instead of either
    | accepting an order they can't fulfil as-is, or rejecting it outright
    | over one item.
    |--------------------------------------------------------------------------
    */
    public ?int $adjustingOrderId = null;

    /** @var array<int, int> order_item_id => quantity being edited */
    public array $adjustQuantities = [];

    /** Baseline used to detect a NEW pending order arriving between polls. */
    public int $lastKnownPendingCount = 0;


    public function mount(): void
    {
        $this->lastKnownPendingCount = Order::where('status', 'pending')->count();
    }


    public function startAdjusting(int $orderId): void
    {
        $order = Order::with('items')
            ->where('status', 'pending')
            ->findOrFail($orderId);

        $this->adjustingOrderId = $orderId;
        $this->adjustQuantities = $order->items->pluck('quantity', 'id')->toArray();
    }

    public function cancelAdjusting(): void
    {
        $this->adjustingOrderId = null;
        $this->adjustQuantities = [];
    }

    public function removeAdjustedItem(int $itemId): void
    {
        unset($this->adjustQuantities[$itemId]);
    }

    public function saveAdjustment(int $orderId): void
    {
        $order = Order::with('items')
            ->where('status', 'pending')
            ->findOrFail($orderId);

        foreach ($order->items as $item) {

            $newQuantity = (int) ($this->adjustQuantities[$item->id] ?? 0);

            if ($newQuantity <= 0) {
                $item->delete();
                continue;
            }

            if ($newQuantity !== $item->quantity) {
                $item->update([
                    'quantity'   => $newQuantity,
                    'line_total' => $item->unit_price * $newQuantity,
                ]);
            }
        }

        // The fee now lives in its own column, so it survives an
        // adjustment untouched — no need to back it out of
        // (total - subtotal), which broke as soon as either value was
        // rewritten. Only delivery orders carry one; pickup and dining
        // are always zero.
        $deliveryFee = $order->order_type === 'delivery'
            ? (float) $order->delivery_fee
            : 0.0;

        $newSubtotal = (float) $order->items()->sum('line_total');

        $order->update([
            'subtotal'     => $newSubtotal,
            'delivery_fee' => $deliveryFee,
            'total'        => $newSubtotal + $deliveryFee,
        ]);

        $this->cancelAdjusting();
    }


    /*
    |--------------------------------------------------------------------------
    | Status Transitions
    |
    | pending -> confirmed -> ready -> completed
    | pending -> cancelled (reject)
    |
    | Each transition only fires from the status it expects, so a
    | double-click or a stale card can't skip a stage or resurrect a
    | rejected order.
    |--------------------------------------------------------------------------
    */

    public function accept(int $orderId): void
    {
        // Pending -> confirmed is "kitchen has accepted this order and
        // will start preparing it." Adjust if your actual kitchen
        // workflow treats "accepted" differently from "confirmed."
        Order::where('id', $orderId)->where('status', 'pending')->update(['status' => 'confirmed']);
    }

    public function reject(int $orderId): void
    {
        // No reason/refund flow exists yet — this just marks it
        // cancelled. If you need a reason captured (out of stock, too
        // busy, etc.), that's a follow-up, not something guessed here.
        Order::where('id', $orderId)->where('status', 'pending')->update(['status' => 'cancelled']);
    }

    public function markReady(int $orderId): void
    {
        // Confirmed -> ready is "kitchen has finished preparing this
        // order" — for delivery, this is normally what triggers a
        // courier dispatch; for pickup, it's what the "we'll text you"
        // note on checkout is referring to. Neither is wired up here.
        Order::where('id', $orderId)->where('status', 'confirmed')->update(['status' => 'ready']);
    }

    public function complete(int $orderId): void
    {
        // Ready -> completed is "handed to the courier / collected by
        // the customer / served at the table." This just closes the
        // order out of the active board.
        Order::where('id', $orderId)->where('status', 'ready')->update(['status' => 'completed']);
    }


    public function render()
    {
        $pendingOrders = Order::with('items')
            ->where('status', 'pending')
            ->oldest() // first-come-first-served, oldest pending order shown first
            ->get();

        $preparingOrders = Order::with('items')
            ->where('status', 'confirmed')
            ->oldest()
            ->get();

        $readyOrders = Order::with('items')
            ->where('status', 'ready')
            ->oldest()
            ->get();

        // A pre-order booked for next Friday shouldn't black out the
        // screen today — it still appears in the New column, it just
        // doesn't demand an immediate accept/reject. Anything for today
        // (or with no schedule at all) is treated as live.
        $takeoverOrders = $pendingOrders->filter(function (Order $order) {
            if (! $order->is_pre_order || ! $order->pre_order_date) {
                return true;
            }

            return Carbon::parse($order->pre_order_date)->isToday();
        })->values();

        // Dispatched as a real browser event the Blade view listens for —
        // NOT via a "$refresh" listener, which Livewire never actually
        // fires, and NOT by comparing a count baked into x-init (which
        // only runs once and would freeze at the page's first-load value).
        // Comparing counts here, on every poll, is the one place this
        // comparison can reliably happen fresh each time.
        if ($pendingOrders->count() > $this->lastKnownPendingCount) {
            $this->dispatch('new-order-received');
        }
        $this->lastKnownPendingCount = $pendingOrders->count();

        return view('livewire.orders.accept-queue', [
            'pendingOrders'   => $pendingOrders,
            'takeoverOrders'  => $takeoverOrders,
            'preparingOrders' => $preparingOrders,
            'readyOrders'     => $readyOrders,
        ]);
    }
}