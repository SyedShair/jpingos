<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryMedia extends Model
{
    protected $table = 'category_media';

    protected $fillable = ['category_id', 'image', 'pdf_menu'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/'.$this->image) : null;
    }

    public function getPdfMenuUrlAttribute(): ?string
    {
        return $this->pdf_menu ? asset('storage/'.$this->pdf_menu) : null;
    }

    public function hasImage(): bool
    {
        return ! is_null($this->image);
    }

    public function hasPdfMenu(): bool
    {
        return ! is_null($this->pdf_menu);
    }
}
