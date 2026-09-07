<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'category_id',
        'price', 'discount_price', 'prep_time_minutes', 'spice_level',
        'is_vegetarian', 'is_vegan', 'is_gluten_free', 'ingredients',
        'is_available', 'is_featured',
    ];

    protected $casts = [
        'price'           => 'decimal:2',
        'discount_price'  => 'decimal:2',
        'is_vegetarian'   => 'boolean',
        'is_vegan'        => 'boolean',
        'is_gluten_free'  => 'boolean',
        'is_available'    => 'boolean',
        'is_featured'     => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (MenuItem $item) {
            if (empty($item->slug)) {
                $item->slug = static::uniqueSlug($item->name);
            }
        });

        static::updating(function (MenuItem $item) {
            if ($item->isDirty('name') && ! $item->isDirty('slug')) {
                $item->slug = static::uniqueSlug($item->name, $item->id);
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** All gallery images, in display order. */
    public function images(): HasMany
    {
        return $this->hasMany(MenuItemImage::class)->orderByDesc('is_primary')->orderBy('sort_order');
    }

    /** The single cover image used in listings/cards. */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(MenuItemImage::class)->orderByDesc('is_primary')->orderBy('sort_order');
    }

    /** Shared, reusable customer customizations (Sauce, Size, Cooking Level, etc.) attached to this dish. */
    public function optionGroups(): BelongsToMany
    {
        return $this->belongsToMany(OptionGroup::class)
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderBy('menu_item_option_group.sort_order');
    }

    /**
     * Cover image URL for listings — the primary image if one's been
     * uploaded, otherwise a neutral placeholder. Load `primaryImage` (or
     * `images`) as an eager-loaded relation to avoid N+1 queries on
     * index pages.
     */
    public function getImageUrlAttribute(): string
    {
        $image = $this->relationLoaded('primaryImage')
            ? $this->primaryImage
            : ($this->relationLoaded('images') ? $this->images->first() : $this->primaryImage()->first());

        return $image?->url ?? asset('storefront/assets/images/products/medium-size/1.jpg');
    }

    public function getDisplayPriceAttribute(): float
    {
        return (float) ($this->discount_price ?? $this->price);
    }

    public function getIsOnSaleAttribute(): bool
    {
        return ! is_null($this->discount_price) && $this->discount_price < $this->price;
    }

    /** Whole-percent discount for sale badges, e.g. "-30%". Null when not on sale. */
    public function getDiscountPercentAttribute(): ?int
    {
        if (! $this->is_on_sale) {
            return null;
        }

        return (int) round((1 - ($this->discount_price / $this->price)) * 100);
    }

    /** Dishes visible to customers — hidden/86'd dishes never reach the storefront. */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public static function spiceLevels(): array
    {
        return [
            'none'   => 'None',
            'mild'   => 'Mild',
            'medium' => 'Medium',
            'hot'    => 'Hot',
        ];
    }
}