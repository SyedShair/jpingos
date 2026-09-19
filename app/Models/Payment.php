<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'method', 'status', 'amount', 'currency',
        'gateway', 'transaction_id', 'gateway_response', 'paid_at',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'gateway_response' => 'array',
        'paid_at'          => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function markPaid(?string $transactionId = null, ?array $gatewayResponse = null): void
    {
        $this->update([
            'status'           => 'paid',
            'transaction_id'   => $transactionId ?? $this->transaction_id,
            'gateway_response' => $gatewayResponse ?? $this->gateway_response,
            'paid_at'          => now(),
        ]);
    }

    public function markFailed(?array $gatewayResponse = null): void
    {
        $this->update([
            'status'           => 'failed',
            'gateway_response' => $gatewayResponse ?? $this->gateway_response,
        ]);
    }
}