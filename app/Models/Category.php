<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id', 'name', 'slug', 'description', 'icon', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = static::uniqueSlug($category->name);
            }
        });

        static::updating(function (Category $category) {
            if ($category->isDirty('name') && ! $category->isDirty('slug')) {
                $category->slug = static::uniqueSlug($category->name, $category->id);
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

    /** The main category this belongs under (e.g. "Mains" → parent "Food Menu"). */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /** Sub-categories nested under this one. */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->ordered();
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /** Only top-level categories (Food Menu, Drink Menu, Dessert Menu, ...). */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Only categories a dish can actually be assigned to — i.e. sub-categories
     * (Mains, Drinks, etc.), never a top-level main category (Food Menu,
     * Drink Menu). Used by MenuItemController::create()/edit() to build the
     * dish's category dropdown.
     */

   

    public function scopeAssignable($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /** "Food Menu → Mains" style label, used in dropdowns and tables. */
    public function getFullNameAttribute(): string
    {
        return $this->parent ? "{$this->parent->name} → {$this->name}" : $this->name;
    }

    public function isTopLevel(): bool
    {
        return is_null($this->parent_id);
    }

    public function media(): HasOne
    {
        return $this->hasOne(\App\Models\CategoryMedia::class);
    }
}