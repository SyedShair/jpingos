<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'row_id',
        'menu_item_id',
        'deal_id',
        'name',
        'slug',
        'quantity',
        'options',
        'unit_price',
    ];

    protected $casts = [
        'options'    => 'array',
        'unit_price' => 'decimal:2',
    ];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}