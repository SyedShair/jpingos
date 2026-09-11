<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone',
        'address', 'apartment', 'city', 'postcode', 'password','email_verified_at'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
    public function hasVerifiedEmail(): bool
{
    return ! is_null($this->email_verified_at);
}

public function markEmailAsVerified(): bool
{
    return $this->forceFill(['email_verified_at' => now()])->save();
}
}