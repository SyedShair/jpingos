<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliverySetting extends Model
{
    protected $fillable = [
        'address',
        'postcode',
        'latitude',
        'longitude',
        'radius_km',
        'is_active',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
        'radius_km' => 'float',
        'is_active' => 'boolean',
    ];

    /**
     * There's only ever one delivery zone right now (a single center +
     * radius), so this is a light singleton helper rather than a full
     * repository — always returns the one row, creating a sensible
     * default the first time it's called if none exists yet.
     */
    public static function current(): self
    {
        return static::firstOrCreate([], [
            'latitude'  => 0,
            'longitude' => 0,
            'radius_km' => 5,
        ]);
    }

    /**
     * Haversine distance in km from the delivery center to an arbitrary
     * point — use this at checkout to check whether a customer's address
     * falls inside the radius.
     */
    public function distanceToKm(float $lat, float $lng): float
    {
        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat - $this->latitude);
        $dLng = deg2rad($lng - $this->longitude);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($this->latitude)) * cos(deg2rad($lat)) * sin($dLng / 2) ** 2;

        return $earthRadiusKm * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    public function isWithinDeliveryArea(float $lat, float $lng): bool
    {
        return $this->distanceToKm($lat, $lng) <= $this->radius_km;
    }
}