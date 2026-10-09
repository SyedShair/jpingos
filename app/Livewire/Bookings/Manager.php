<?php

namespace App\Livewire\Bookings;

use App\Models\Booking;
use App\Services\GuestplanService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Bookings')]
class Manager extends Component
{
    use WithPagination;

    public string $statusFilter = '';
    public string $dateFilter = '';
    public string $search = '';
    public ?Booking $selectedBooking = null;

    public function mount(): void
    {
        $this->dateFilter = now()->toDateString();
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }
    public function updatingDateFilter(): void { $this->resetPage(); }

    public function showToday(): void { $this->dateFilter = now()->toDateString(); $this->resetPage(); }
    public function showAllDates(): void { $this->dateFilter = ''; $this->resetPage(); }

    public function view(int $id): void
    {
        $this->selectedBooking = Booking::with('event')->findOrFail($id);
    }

    public function closeDetail(): void
    {
        $this->selectedBooking = null;
    }

    /** DB only — deliberately never calls GuestPlan (per current requirements). */
    public function updateStatus(int $id, string $status): void
    {
        Booking::findOrFail($id)->update(['status' => $status]);

        if ($this->selectedBooking?->id === $id) {
            $this->selectedBooking->refresh();
        }
    }

    /** Manually push a locally-pending or previously failed booking to GuestPlan. */
    public function retrySync(int $id, GuestplanService $guestplan): void
    {
        $booking = Booking::findOrFail($id);
        $result = $guestplan->createBooking($booking);

        if ($result['ok']) {
            $booking->update([
                'status'                => 'confirmed',
                'guestplan_booking_id'   => $result['guestplan_booking_id'],
                'guestplan_error'        => null,
                'synced_at'              => now(),
            ]);
            session()->flash('status-message', 'Synced to GuestPlan.');
        } else {
            $booking->update(['guestplan_error' => $result['error']]);
            session()->flash('status-message', 'GuestPlan sync failed: ' . $result['error']);
        }

        if ($this->selectedBooking?->id === $id) {
            $this->selectedBooking->refresh();
        }
    }

    /** Soft-delete only — never cancels in GuestPlan (per current requirements). */
    public function delete(int $id): void
    {
        Booking::findOrFail($id)->delete();

        if ($this->selectedBooking?->id === $id) {
            $this->selectedBooking = null;
        }

        session()->flash('status-message', 'Booking deleted.');
    }

    public function render()
    {
        $bookings = Booking::with('event')
            ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->dateFilter !== '', fn ($q) => $q->whereDate('booking_date', $this->dateFilter))
            ->when($this->search !== '', function ($q) {
                $q->where(function ($q) {
                    $q->where('booking_number', 'like', "%{$this->search}%")
                        ->orWhere('customer_name', 'like', "%{$this->search}%")
                        ->orWhere('customer_email', 'like', "%{$this->search}%")
                        ->orWhere('customer_phone', 'like', "%{$this->search}%")
                        ->orWhere('guestplan_booking_id', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('booking_date')->orderBy('booking_time')
            ->paginate(15);

        return view('livewire.bookings.manager', [
            'bookings' => $bookings,
            'statuses' => Booking::STATUSES,
        ]);
    }
}
