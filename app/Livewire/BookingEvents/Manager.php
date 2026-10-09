<?php

namespace App\Livewire\BookingEvents;

use App\Models\BookingEvent;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Booking Events')]
class Manager extends Component
{
    use WithFileUploads;

    public bool $showForm = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $description = '';
    public string $starts_on = '';
    public string $ends_on = '';
    public int $party_size_min = 1;
    public int $party_size_max = 12;
    public ?int $capacity = null;
    public bool $is_active = true;
    public $image;              // new upload, optional
    public ?string $existingImage = null;

    protected function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:150'],
            'description'    => ['nullable', 'string', 'max:2000'],
            'starts_on'      => ['required', 'date'],
            'ends_on'        => ['required', 'date', 'after_or_equal:starts_on'],
            'party_size_min' => ['required', 'integer', 'min:1'],
            'party_size_max' => ['required', 'integer', 'gte:party_size_min'],
            'capacity'       => ['nullable', 'integer', 'min:1'],
            'is_active'      => ['boolean'],
            'image'          => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $event = BookingEvent::findOrFail($id);

        $this->editingId      = $event->id;
        $this->name           = $event->name;
        $this->description    = (string) $event->description;
        $this->starts_on      = $event->starts_on->toDateString();
        $this->ends_on        = $event->ends_on->toDateString();
        $this->party_size_min = $event->party_size_min;
        $this->party_size_max = $event->party_size_max;
        $this->capacity       = $event->capacity;
        $this->is_active      = $event->is_active;
        $this->existingImage  = $event->image;
        $this->image          = null;

        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        unset($data['image']);

        if ($this->image) {
            $data['image'] = $this->image->store('booking-events', 'public');
        }

        if ($this->editingId) {
            $event = BookingEvent::findOrFail($this->editingId);

            if ($this->image && $event->image) {
                Storage::disk('public')->delete($event->image);
            }

            $event->update($data);
            session()->flash('status-message', 'Event updated.');
        } else {
            BookingEvent::create($data);
            session()->flash('status-message', 'Event created.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function toggleActive(int $id): void
    {
        $event = BookingEvent::findOrFail($id);
        $event->update(['is_active' => ! $event->is_active]);
    }

    public function delete(int $id): void
    {
        $event = BookingEvent::findOrFail($id);

        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();
        session()->flash('status-message', 'Event deleted.');
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId', 'name', 'description', 'starts_on', 'ends_on',
            'party_size_min', 'party_size_max', 'capacity', 'is_active', 'image', 'existingImage',
        ]);
        $this->party_size_min = 1;
        $this->party_size_max = 12;
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.booking-events.manager', [
            'events' => BookingEvent::orderBy('sort_order')->orderByDesc('starts_on')->get(),
        ]);
    }
}
