<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class BusinessHour extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_closed'       => 'boolean',
        'closes_next_day' => 'boolean',
    ];

    public const DAY_NAMES = [0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];

    /** All 7 rows, keyed by day_of_week (0–6), cached briefly since these change rarely. */
    public static function week(): \Illuminate\Support\Collection
    {
        return Cache::remember('business_hours:week', now()->addHour(), function () {
            return static::orderBy('day_of_week')->get()->keyBy('day_of_week');
        });
    }

    public static function forgetCache(): void
    {
        Cache::forget('business_hours:week');
    }

    /** Same shape the storefront JS reads — see booking.blade.php's `hours` JSON. */
    public static function forFrontend(): array
    {
        return static::week()->map(fn (self $h) => [
            'day'             => $h->day_of_week,
            'closed'          => $h->is_closed,
            'opens'           => $h->opens_at ? substr($h->opens_at, 0, 5) : null,
            'closes'          => $h->closes_at ? substr($h->closes_at, 0, 5) : null,
            'closesNextDay'   => $h->closes_next_day,
        ])->values()->all();
    }

    /**
     * Is the restaurant open at this date + time? Handles a closing time that
     * rolls past midnight (e.g. Friday 18:00–01:00 covers 00:30 Saturday too).
     */
    public static function isOpenAt(string $date, string $time): bool
    {
        $target = Carbon::parse("{$date} {$time}");
        $dow = (int) $target->format('w');
        $today = static::week()->get($dow);

        if ($today && ! $today->is_closed && $today->opens_at && $today->closes_at) {
            $opens = Carbon::parse($date . ' ' . $today->opens_at);
            $closes = Carbon::parse($date . ' ' . $today->closes_at);
            if ($today->closes_next_day || $closes->lt($opens)) {
                $closes->addDay();
            }
            if ($target->between($opens, $closes)) {
                return true;
            }
        }

        // Also check yesterday's hours in case they roll into today (e.g. it's
        // 00:30 today but that's still "last night's" service).
        $yesterday = static::week()->get(($dow + 6) % 7);
        if ($yesterday && ! $yesterday->is_closed && $yesterday->opens_at && $yesterday->closes_at && ($yesterday->closes_next_day || $yesterday->closes_at < $yesterday->opens_at)) {
            $prevDate = $target->copy()->subDay()->toDateString();
            $opens = Carbon::parse($prevDate . ' ' . $yesterday->opens_at);
            $closes = Carbon::parse($prevDate . ' ' . $yesterday->closes_at)->addDay();
            if ($target->between($opens, $closes)) {
                return true;
            }
        }

        return false;
    }

    /** Human-readable hours for one weekday, e.g. "11:00 AM – 10:00 PM" or "Closed". */
    public static function describe(int $dayOfWeek): string
    {
        $h = static::week()->get($dayOfWeek);

        if (! $h || $h->is_closed || ! $h->opens_at || ! $h->closes_at) {
            return 'Closed';
        }

        return Carbon::parse($h->opens_at)->format('g:i A') . ' – ' . Carbon::parse($h->closes_at)->format('g:i A')
            . ($h->closes_next_day ? ' (next day)' : '');
    }
}
