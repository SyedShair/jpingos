<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Manager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $typeFilter = '';

    /**
     * Which day's orders to show. Defaults to today, since the kitchen
     * almost always cares about today's service rather than the whole
     * history — an explicit "All dates" option switches it off.
     *
     * '' = all dates, otherwise a Y-m-d string.
     */
    public string $dateFilter = '';

    public ?int $selectedOrderId = null;

    protected $queryString = ['search', 'statusFilter', 'typeFilter', 'dateFilter'];

    public const STATUSES = ['pending', 'confirmed', 'preparing', 'ready', 'completed', 'cancelled'];

    /** dining = a pre-order table reservation: no address, no delivery fee. */
    public const ORDER_TYPES = ['delivery', 'pickup', 'dining'];

    /** Matches the plain-string status column on the payments table. */
    public const PAYMENT_STATUSES = ['pending', 'paid', 'failed', 'refunded', 'cancelled'];

    /**
     * Seed the date filter to today on first load. Done in mount() rather
     * than as a property default so that a queryString value in the URL
     * (e.g. a bookmarked "all dates" or a specific day) still wins.
     */
    public function mount(): void
    {
        if (! request()->has('dateFilter')) {
            $this->dateFilter = Carbon::today()->toDateString();
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function showToday(): void
    {
        $this->dateFilter = Carbon::today()->toDateString();
        $this->resetPage();
    }

    public function showAllDates(): void
    {
        $this->dateFilter = '';
        $this->resetPage();
    }

    public function updateStatus(int $orderId, string $status): void
    {
        if (! in_array($status, self::STATUSES, true)) {
            return;
        }

        Order::whereKey($orderId)->update(['status' => $status]);

        session()->flash('status-message', 'Order updated.');
    }

    /**
     * Lets staff record the real-world outcome of a payment — most often
     * marking a Cash on Delivery order "paid" once the driver returns
     * with the cash, or "failed"/"cancelled" if the customer wasn't there
     * or refused. Once a card gateway exists, its webhook would call the
     * same Payment::markPaid()/markFailed() methods automatically; this
     * is the manual equivalent for cash.
     *
     * Uses $order->latestPayment (hasOne ->latestOfMany()) so this always
     * targets the most recent payment attempt, even if a retry exists.
     */
    public function updatePaymentStatus(int $orderId, string $status): void
    {
        if (! in_array($status, self::PAYMENT_STATUSES, true)) {
            return;
        }

        $order = Order::with('latestPayment')->find($orderId);
        $payment = $order?->latestPayment;

        if (! $payment) {
            session()->flash('status-message', 'This order has no payment record to update.');
            return;
        }

        match ($status) {
            'paid'   => $payment->markPaid(),
            'failed' => $payment->markFailed(),
            default  => $payment->update(['status' => $status]),
        };

        session()->flash('status-message', 'Payment status updated.');
    }

    public function viewOrder(int $orderId): void
    {
        $this->selectedOrderId = $orderId;
    }

    public function closeDetail(): void
    {
        $this->selectedOrderId = null;
    }

    public function render()
    {
        $orders = Order::query()
            ->with('latestPayment')
            ->when($this->search !== '', function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($q) use ($term) {
                    $q->where('order_number', 'like', $term)
                      ->orWhere('email', 'like', $term)
                      ->orWhere('first_name', 'like', $term)
                      ->orWhere('last_name', 'like', $term)
                      ->orWhere('phone', 'like', $term);
                });
            })
            ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->typeFilter !== '', fn ($q) => $q->where('order_type', $this->typeFilter))
            ->when($this->dateFilter !== '', fn ($q) => $q->whereDate('created_at', $this->dateFilter))
            ->latest()
            ->paginate(15);

        $selectedOrder = $this->selectedOrderId
            ? Order::with(['items', 'latestPayment'])->find($this->selectedOrderId)
            : null;

        return view('livewire.orders.manager', [
            'orders'          => $orders,
            'selectedOrder'   => $selectedOrder,
            'statuses'        => self::STATUSES,
            'orderTypes'      => self::ORDER_TYPES,
            'paymentStatuses' => self::PAYMENT_STATUSES,
            'isToday'         => $this->dateFilter === Carbon::today()->toDateString(),
        ]);
    }
}