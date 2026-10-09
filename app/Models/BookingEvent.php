<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BookingEvent extends Model
{
    protected $guarded = [];

    protected $casts = [
        'starts_on'  => 'date',
        'ends_on'    => 'date',
        'is_active'  => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (BookingEvent $event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->name) . '-' . Str::random(4);
            }
        });
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** Events visible on the storefront right now: active and today falls within their date range. */
    public function scopeCurrentlyRunning($query)
    {
        $today = now()->toDateString();

        return $query->where('is_active', true)
            ->whereDate('starts_on', '<=', $today)
            ->whereDate('ends_on', '>=', $today)
            ->orderBy('sort_order');
    }

    public function imageUrl(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function isFull(): bool
    {
        if ($this->capacity === null) {
            return false;
        }

        return $this->bookings()->where('status', '!=', 'cancelled')->sum('party_size') >= $this->capacity;
    }
}
