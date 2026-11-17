<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyCoupon extends Model
{
    protected $fillable = [
        'code', 'email', 'discount_type', 'discount_value', 'expires_at',
        'used_at', 'used_order_id', 'voided_at', 'emailed_at',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'expires_at'     => 'datetime',
        'used_at'        => 'datetime',
        'voided_at'      => 'datetime',
        'emailed_at'     => 'datetime',
    ];

    public function usedOrder()
    {
        return $this->belongsTo(Order::class, 'used_order_id');
    }

    /** 'used' | 'voided' | 'expired' | 'active' */
    public function getStatusAttribute(): string
    {
        if ($this->used_at) {
            return 'used';
        }

        if ($this->voided_at) {
            return 'voided';
        }

        if ($this->expires_at->isPast()) {
            return 'expired';
        }

        return 'active';
    }

    /** "10% off" or "£5 off" — for emails and screens. */
    public function getDiscountLabelAttribute(): string
    {
        $value = rtrim(rtrim(number_format((float) $this->discount_value, 2, '.', ''), '0'), '.');

        return $this->discount_type === 'percent' ? $value . '% off' : '£' . $value . ' off';
    }

    public function scopeRedeemable($query)
    {
        return $query->whereNull('used_at')->whereNull('voided_at')->where('expires_at', '>', now());
    }
}