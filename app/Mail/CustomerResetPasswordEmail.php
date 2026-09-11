<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class CustomerResetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $resetUrl;

    public function __construct(public Customer $customer)
    {
        $this->resetUrl = URL::temporarySignedRoute(
            'storefront.password.reset.form',
            now()->addMinutes(60),
            ['customer' => $customer->id]
        );
    }

    public function build()
    {
        return $this->subject('Reset Your Password')
            ->view('storefront.emails.forget-password');
    }
}