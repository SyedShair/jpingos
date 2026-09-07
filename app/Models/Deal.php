<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Deal extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'image', 'type', 'discount_type',
        'discount_value', 'combo_price', 'min_spend',
        'buy_quantity', 'get_quantity', 'get_discount_percent',
        'promo_code', 'starts_at', 'ends_at',
        'recurring_days', 'daily_start_time', 'daily_end_time',
        'usage_limit', 'used_count', 'is_active',
    ];

    protected $casts = [
        'discount_value'        => 'decimal:2',
        'combo_price'           => 'decimal:2',
        'min_spend'             => 'decimal:2',
        'buy_quantity'          => 'integer',
        'get_quantity'          => 'integer',
        'get_discount_percent'  => 'integer',
        'starts_at'             => 'datetime',
        'ends_at'               => 'datetime',
        'recurring_days'        => 'array',
        'usage_limit'           => 'integer',
        'used_count'            => 'integer',
        'is_active'             => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Deal $deal) {
            if (empty($deal->slug)) {
                $deal->slug = static::uniqueSlug($deal->name);
            }
        });

        static::updating(function (Deal $deal) {
            if ($deal->isDirty('name') && ! $deal->isDirty('slug')) {
                $deal->slug = static::uniqueSlug($deal->name, $deal->id);
            }
        });
    }

    protected static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            static::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function items(): HasMany
    {
        return $this->hasMany(DealItem::class);
    }

    public function appliesToItems(): HasMany
    {
        return $this->items()->where('role', 'applies_to');
    }

    public function buyItems(): HasMany
    {
        return $this->items()->where('role', 'buy');
    }

    public function freeItems(): HasMany
    {
        return $this->items()->where('role', 'free');
    }

    public function bundleComponents(): HasMany
    {
        return $this->items()->where('role', 'bundle_component');
    }

    public static function typeGroups(): array
    {
        return [
            'Daily & Time-Based Deals' => [
                'flash_deal'    => 'Flash Deal / Daily Special',
                'happy_hour'    => 'Happy Hour',
                'lunch_special' => 'Lunch Special',
            ],
            'Conditional Offers' => [
                'tiered_spend' => 'Tiered Spend Discount',
                'bogo'         => 'Buy One, Get One (BOGO)',
                'free_gift'    => 'Free Gift with Purchase',
            ],
            'Bundled Offers & Combos' => [
                'combo'       => 'Meal Combo',
                'bundle'      => 'Family Bundle',
                'promo_code'  => 'Promo Code',
            ],
        ];
    }

    public static function typeLabel(string $type): string
    {
        foreach (static::typeGroups() as $group) {
            if (isset($group[$type])) {
                return $group[$type];
            }
        }

        return ucfirst(str_replace('_', ' ', $type));
    }

    /**
     * Full public URL for the deal's own uploaded image. Falls back to the
     * first bundle component's photo (via bundleThumbnailUrl()) for
     * combo/bundle deals that don't have a dedicated image, and finally to
     * a generic placeholder — so a card can always call this without a
     * null check.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/'.$this->image);
        }

        if (in_array($this->type, ['combo', 'bundle'], true)) {
            return $this->bundleThumbnailUrl();
        }

        return asset('storefront/assets/images/products/medium-size/1.jpg');
    }

    /**
     * True only within the deal's live window right now: active flag on,
     * inside the overall start/end range, under its usage cap, and (for
     * recurring deals) on the right day and inside today's time window.
     */
    public function isCurrentlyActive(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if (! empty($this->recurring_days)) {
            if (! in_array((int) $now->dayOfWeek, array_map('intval', $this->recurring_days), true)) {
                return false;
            }
        }

        if ($this->daily_start_time && $this->daily_end_time) {
            $nowTime = $now->format('H:i:s'); // 24-hour, zero-padded — safe to compare as strings
            if ($nowTime < $this->daily_start_time || $nowTime > $this->daily_end_time) {
                return false;
            }
        }

        return true;
    }

    /** True once the deal's hard end date/time has passed, regardless of the is_active flag. */
    public function isExpired(): bool
    {
        return (bool) ($this->ends_at && Carbon::now()->gt($this->ends_at));
    }

    /** True if the deal's start date/time hasn't arrived yet. */
    public function hasNotStartedYet(): bool
    {
        return (bool) ($this->starts_at && Carbon::now()->lt($this->starts_at));
    }

    /**
     * Single source of truth for the admin badge — avoids the earlier bug
     * where an expired-but-still-`is_active` deal showed as "Scheduled".
     */
    public function statusLabel(): string
    {
        if (! $this->is_active) {
            return 'Paused';
        }

        if ($this->isExpired()) {
            return 'Expired';
        }

        if ($this->isCurrentlyActive()) {
            return 'Live now';
        }

        if ($this->hasNotStartedYet()) {
            return 'Upcoming';
        }

        // Active, within its overall date range, but outside today's
        // recurring window (e.g. Happy Hour before 4 PM).
        return 'Scheduled';
    }

    public function statusBadgeClass(): string
    {
        return match ($this->statusLabel()) {
            'Live now' => 'success',
            'Upcoming', 'Scheduled' => 'warning',
            'Expired', 'Paused' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Human-readable schedule summary for the admin list — always
     * 24-hour time (e.g. "Mon, Wed, Fri · 16:00–18:00"), never AM/PM.
     */
    public function scheduleSummary(): string
    {
        $parts = [];

        if (! empty($this->recurring_days)) {
            $names = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            $days = array_map(fn ($d) => $names[$d] ?? '?', $this->recurring_days);
            $parts[] = implode(', ', $days);
        }

        if ($this->daily_start_time && $this->daily_end_time) {
            $parts[] = Carbon::parse($this->daily_start_time)->format('H:i')
                .'–'.Carbon::parse($this->daily_end_time)->format('H:i');
        }

        if ($this->starts_at || $this->ends_at) {
            $parts[] = ($this->starts_at?->format('M j, Y H:i') ?? 'now')
                .' – '.($this->ends_at?->format('M j, Y H:i') ?? 'ongoing');
        }

        return $parts ? implode(' · ', $parts) : 'Always on';
    }

    /**
     * Discounted price for a given base price, using this deal's own
     * discount_type/discount_value. Only meaningful for deal types that
     * validate discount_type as percentage/fixed_amount (flash_deal,
     * happy_hour, lunch_special, promo_code) — other types (bogo, combo,
     * free_gift) don't represent a simple per-item price cut this way.
     */
    public function discountedPriceFor(float $price): float
    {
        return match ($this->discount_type) {
            'percentage'   => max(0, round($price * (1 - ($this->discount_value / 100)), 2)),
            'fixed_amount' => max(0, round($price - $this->discount_value, 2)),
            // combo/bundle deals are saved with discount_type = 'fixed_price'
            // (see Manager::discountTypeForCurrentDealType()). There's no
            // per-item price for these — the whole bundle is combo_price —
            // so this is only here as a safety net for any caller that
            // passes a bundle deal in by mistake; it returns the bundle's
            // own price instead of silently falling through to $price,
            // which is the bug that made deal prices never show for
            // combo/bundle cards.
            'fixed_price'  => (float) $this->combo_price,
            default        => $price,
        };
    }

    /** "-30%" or "-£5.00" badge text, or null if there's no discount to show. */
    public function discountBadgeLabel(): ?string
    {
        if (! $this->discount_value || (float) $this->discount_value <= 0) {
            return null;
        }

        return match ($this->discount_type) {
            'percentage'   => '-'.rtrim(rtrim(number_format((float) $this->discount_value, 2), '0'), '.').'%',
            'fixed_amount' => '-£'.number_format((float) $this->discount_value, 2),
            default        => null,
        };
    }

    /**
     * When this deal's current countdown ends: the deal's own ends_at for
     * flash_deal-style deals, or today's daily_end_time for a recurring
     * Happy Hour/Lunch Special window. Null if neither is set.
     */
    public function countdownTarget(): ?Carbon
    {
        if ($this->ends_at) {
            return $this->ends_at;
        }

        if ($this->daily_end_time) {
            return Carbon::today()->setTimeFromTimeString($this->daily_end_time);
        }

        return null;
    }

    /**
     * Sum of what the bundle's components would cost individually — used
     * as the "old" struck-through price next to combo_price. Uses each
     * component's per-item override_price if set, otherwise the menu
     * item's normal price, multiplied by quantity.
     */
    public function bundleOriginalPrice(): float
    {
        return $this->bundleComponents
            ->filter(fn ($dealItem) => $dealItem->menuItem)
            ->sum(fn ($dealItem) => (float) ($dealItem->override_price ?? $dealItem->menuItem->price) * $dealItem->quantity);
    }

    /** "Burger + Fries + Drink" style summary of what's included. */
    public function bundleItemNames(): string
    {
        return $this->bundleComponents
            ->filter(fn ($dealItem) => $dealItem->menuItem)
            ->map(fn ($dealItem) => $dealItem->menuItem->name)
            ->implode(' + ');
    }

    /** First component's photo, standing in as the bundle's thumbnail. */
    public function bundleThumbnailUrl(): string
    {
        $first = $this->bundleComponents->first(fn ($dealItem) => $dealItem->menuItem);

        return $first?->menuItem->image_url ?? asset('storefront/assets/images/products/medium-size/1.jpg');
    }
}