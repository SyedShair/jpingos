<?php

namespace App\Models;

use App\Services\LoyaltyService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'customer_id',
        'first_name', 'last_name', 'email', 'phone',
        'address', 'apartment', 'city', 'postcode',
        'order_type', 'notes',
        'subtotal', 'total', 'status','is_pre_order', 'pre_order_date', 'pre_order_time','delivery_fee',
        'coupon_id', 'coupon_code', 'discount_total',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total'    => 'decimal:2',
        'discount_total' => 'decimal:2',
        'is_pre_order' => 'boolean',
    ];

    // URL/route-model-binding uses order_number, not id — the
    // confirmation route below binds on this.
    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (! $order->order_number) {
                $order->order_number = static::generateOrderNumber();
            }
        });

        // When an order becomes "completed", check whether it earns the
        // customer a loyalty coupon. This only fires for Eloquent saves
        // ($order->update(...)), NOT for bulk Order::where(...)->update(...)
        // — which is why AcceptQueue::complete() and Manager::updateStatus()
        // load the order first. Never allowed to break the status change
        // itself: a failure is logged and the order still completes.
        static::updated(function (Order $order) {
            if ($order->wasChanged('status') && $order->status === 'completed') {
                try {
                    app(LoyaltyService::class)->orderCompleted($order);
                } catch (\Throwable $e) {
                    Log::error('Loyalty coupon check failed', [
                        'order_number' => $order->order_number,
                        'error'        => $e->getMessage(),
                    ]);
                }
            }
        });
    }

    protected static function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . strtoupper(Str::random(8));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

     public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Multiple attempts are possible (a failed card charge followed by
    // a retry) — this is always the one that actually matters.
    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function isPaid(): bool
    {
        return $this->payments()->where('status', 'paid')->exists();
    }

    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}