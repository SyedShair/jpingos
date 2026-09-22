<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'lat'        => 'float',
        'lng'        => 'float',
    ];

    /** ISO country code -> flag emoji (falls back to a globe). */
    public static function flag(?string $code): string
    {
        if (! $code || strlen($code) !== 2) {
            return '🌐';
        }

        $code = strtoupper($code);

        return mb_chr(0x1F1E6 + ord($code[0]) - 65) . mb_chr(0x1F1E6 + ord($code[1]) - 65);
    }
}
