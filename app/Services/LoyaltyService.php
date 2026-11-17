<?php

namespace App\Services;

use App\Mail\LoyaltyCouponMail;
use App\Models\LoyaltyCoupon;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LoyaltyService
{
    /**
     * Called whenever an order becomes "completed" (see Order::booted()).
     *
     * Looks at every completed order for this customer's email that has not
     * yet been used toward a coupon. Once there are enough, the oldest N are
     * stamped as "used up" and a coupon is issued — that stamping IS the
     * reset: the next coupon needs N brand-new orders.
     *
     * Safe to call more than once for the same order: stamped orders are
     * never counted again, so a repeat call finds too few and does nothing.
     */
    public function orderCompleted(Order $order): ?LoyaltyCoupon
    {
        $email = $this->normalizeEmail($order->email);

        // The order that redeemed a reward doesn't earn progress toward the next one.
        if ($email === '' || $order->coupon_id) {
            return null;
        }

        $required = max(1, (int) config('loyalty.orders_required', 5));

        $coupon = DB::transaction(function () use ($email, $required) {
            // Locked so two orders completing at the same moment can't both
            // see "5 uncounted" and each issue a coupon.
            $uncountedIds = $this->uncountedOrders($email)->lockForUpdate()->pluck('id');

            if ($uncountedIds->count() < $required) {
                return null;
            }

            $coupon = LoyaltyCoupon::create([
                'code'           => $this->generateCode(),
                'email'          => $email,
                'discount_type'  => config('loyalty.discount_type', 'percent'),
                'discount_value' => config('loyalty.discount_value', 10),
                'expires_at'     => now()->addDays((int) config('loyalty.expires_in_days', 60)),
            ]);

            Order::whereIn('id', $uncountedIds->take($required)->all())
                ->update(['counted_for_coupon_id' => $coupon->id]);

            return $coupon;
        });

        // Outside the transaction: a slow or failing mail server must not
        // be able to undo the coupon itself.
        if ($coupon) {
            $this->sendEmail($coupon);
        }

        return $coupon;
    }

    /**
     * Used by the checkout page's "Apply" button AND again by
     * OrderController when the order is placed.
     *
     * @return array{valid: bool, message?: string, coupon?: LoyaltyCoupon, discount?: float}
     */
    public function validateForCheckout(string $code, string $email, float $subtotal): array
    {
        $code  = Str::upper(trim($code));
        $email = $this->normalizeEmail($email);

        $coupon = LoyaltyCoupon::where('code', $code)->first();

        // Same message for "no such code" and "someone else's code", so the
        // checkout can't be used to find out which codes exist.
        if (! $coupon || $coupon->email !== $email) {
            return ['valid' => false, 'message' => "That code isn't valid for this email address."];
        }

        if ($coupon->used_at) {
            return ['valid' => false, 'message' => 'This code has already been used.'];
        }

        if ($coupon->voided_at) {
            return ['valid' => false, 'message' => 'This code is no longer valid.'];
        }

        if ($coupon->expires_at->isPast()) {
            return ['valid' => false, 'message' => 'This code expired on ' . $coupon->expires_at->format('j F Y') . '.'];
        }

        $discount = $this->discountFor($coupon, $subtotal);

        if ($discount <= 0) {
            return ['valid' => false, 'message' => 'This code cannot be applied to an empty order.'];
        }

        return ['valid' => true, 'coupon' => $coupon, 'discount' => $discount];
    }

    /** Takes the discount off the food subtotal only — never the delivery fee, never below zero. */
    public function discountFor(LoyaltyCoupon $coupon, float $subtotal): float
    {
        $value = (float) $coupon->discount_value;

        $discount = $coupon->discount_type === 'percent'
            ? $subtotal * ($value / 100)
            : $value;

        return round(max(0, min($discount, $subtotal)), 2);
    }

    /**
     * Marks the coupon used by this order. A single conditional UPDATE, so
     * if two requests race (a double-click, two tabs), exactly one wins and
     * the other gets false.
     */
    public function redeem(LoyaltyCoupon $coupon, Order $order): bool
    {
        return LoyaltyCoupon::whereKey($coupon->id)
            ->redeemable()
            ->update(['used_at' => now(), 'used_order_id' => $order->id]) === 1;
    }

    /** Sends (or re-sends) the coupon email. Never throws. */
    public function sendEmail(LoyaltyCoupon $coupon): bool
    {
        try {
            Mail::to($coupon->email)->send(new LoyaltyCouponMail($coupon));

            $coupon->forceFill(['emailed_at' => now()])->save();

            return true;
        } catch (\Throwable $e) {
            Log::error('Loyalty coupon email failed', [
                'code'  => $coupon->code,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    protected function uncountedOrders(string $email)
    {
        return Order::query()
            ->where('email', $email)
            ->where('status', 'completed')
            ->whereNull('counted_for_coupon_id')
            ->whereNull('coupon_id')
            ->when(config('loyalty.counting_starts_at'), fn ($q, $date) => $q->where('created_at', '>=', $date))
            ->orderBy('id');
    }

    protected function normalizeEmail(?string $email): string
    {
        return Str::lower(trim((string) $email));
    }

    /** FLAME-7K3QX9 — no 0/O/1/I, so a code read off a screen or an email can't be mistyped. */
    public function generateCode(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $prefix   = config('loyalty.code_prefix', 'FLAME');

        do {
            $suffix = '';
            for ($i = 0; $i < 6; $i++) {
                $suffix .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }

            $code = $prefix . '-' . $suffix;
        } while (LoyaltyCoupon::where('code', $code)->exists());

        return $code;
    }
}