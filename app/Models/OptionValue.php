<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OptionValue extends Model
{
    protected $fillable = [
        'option_group_id', 'name', 'price_delta', 'sort_order',
    ];

    protected $casts = [
        'price_delta' => 'decimal:2',
        'sort_order'  => 'integer',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(OptionGroup::class, 'option_group_id');
    }
}