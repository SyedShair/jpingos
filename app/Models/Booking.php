<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Booking extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'booking_date' => 'date',
    ];

    public const STATUSES = ['pending', 'confirmed', 'failed', 'cancelled'];

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            if (empty($booking->booking_number)) {
                $booking->booking_number = 'BK-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(BookingEvent::class, 'booking_event_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.' . config('auth.guards.customer.provider') . '.model'));
    }
}
