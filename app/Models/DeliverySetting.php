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
        'base_price',
        'base_km',
        'per_km_price',
        'max_delivery_fee',
        'is_active',
    ];

    protected $casts = [
        'latitude'         => 'float',
        'longitude'        => 'float',
        'radius_km'        => 'float',
        'base_price'       => 'float',
        'base_km'          => 'float',
        'per_km_price'     => 'float',
        'max_delivery_fee' => 'float',
        'is_active'        => 'boolean',
    ];

    /**
     * Singleton helper to get or initialize the setting row.
     */
    public static function current(): self
    {
        return static::firstOrCreate([], [
            'latitude'         => 0,
            'longitude'        => 0,
            'radius_km'        => 5,
            'base_price'       => 2.50,
            'base_km'          => 1.0,
            'per_km_price'     => 1.00,
            'max_delivery_fee' => null,
            'is_active'        => true,
        ]);
    }

    /**
     * Straight-line distance (Haversine) in kilometers.
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

    /**
     * Check if coordinates fall within the allowed delivery radius.
     */
    public function isWithinDeliveryArea(float $lat, float $lng): bool
    {
        return $this->is_active && $this->distanceToKm($lat, $lng) <= $this->radius_km;
    }

    /**
     * Calculate delivery fee based on customer's coordinates.
     * Returns null if out of radius or delivery is disabled.
     */
    public function calculateFee(float $lat, float $lng): ?float
    {
        if (!$this->isWithinDeliveryArea($lat, $lng)) {
            return null;
        }

        $distanceKm = $this->distanceToKm($lat, $lng);

        // Within base threshold (e.g. 1 km)
        if ($distanceKm <= $this->base_km) {
            return round($this->base_price, 2);
        }

        // Additional distance charged at per_km_price rate
        $extraKm = $distanceKm - $this->base_km;
        $fee = $this->base_price + ($extraKm * $this->per_km_price);

        // Apply maximum cap if defined
        if ($this->max_delivery_fee && $fee > $this->max_delivery_fee) {
            return round($this->max_delivery_fee, 2);
        }

        return round($fee, 2);
    }
}