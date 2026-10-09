<?php

namespace App\Livewire\BusinessHours;

use App\Models\BusinessHour;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Opening Hours')]
class Manager extends Component
{
    /** One entry per day: ['closed' => bool, 'opens' => 'HH:MM', 'closes' => 'HH:MM', 'closes_next_day' => bool] */
    public array $days = [];

    public function mount(): void
    {
        $week = BusinessHour::week();

        foreach (range(0, 6) as $dow) {
            $h = $week->get($dow);
            $this->days[$dow] = [
                'closed'          => $h?->is_closed ?? false,
                'opens'           => $h?->opens_at ? substr($h->opens_at, 0, 5) : '11:00',
                'closes'          => $h?->closes_at ? substr($h->closes_at, 0, 5) : '22:00',
                'closes_next_day' => $h?->closes_next_day ?? false,
            ];
        }
    }

    public function copyToAll(int $fromDay): void
    {
        $source = $this->days[$fromDay];

        foreach ($this->days as $dow => &$day) {
            if ($dow !== $fromDay) {
                $day = $source;
            }
        }
    }

    public function save(): void
    {
        $this->validate([
            'days.*.closed'          => ['boolean'],
            'days.*.opens'           => ['required_if:days.*.closed,false', 'date_format:H:i'],
            'days.*.closes'          => ['required_if:days.*.closed,false', 'date_format:H:i'],
            'days.*.closes_next_day' => ['boolean'],
        ]);

        foreach ($this->days as $dow => $day) {
            BusinessHour::updateOrCreate(
                ['day_of_week' => $dow],
                [
                    'is_closed'       => $day['closed'],
                    'opens_at'        => $day['closed'] ? null : $day['opens'],
                    'closes_at'       => $day['closed'] ? null : $day['closes'],
                    'closes_next_day' => $day['closed'] ? false : $day['closes_next_day'],
                ]
            );
        }

        BusinessHour::forgetCache();
        session()->flash('status-message', 'Opening hours updated.');
    }

    public function render()
    {
        return view('livewire.business-hours.manager', [
            'dayNames' => BusinessHour::DAY_NAMES,
        ]);
    }
}
