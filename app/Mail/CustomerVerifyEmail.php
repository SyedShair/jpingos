<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class CustomerVerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $verifyUrl;

    public function __construct(public Customer $customer)
    {
        // Signed, time-limited URL — can't be forged or replayed after
        // expiry, unlike a raw encrypt($id) link that never expires and
        // is guessable/replayable by anyone who intercepts it.
        $this->verifyUrl = URL::temporarySignedRoute(
            'storefront.verification.verify',
            now()->addMinutes(60),
            ['customer' => $customer->id]
        );
    }

    public function build()
    {
        return $this->subject('Confirm Your Email Address')
            ->view('storefront.emails.verify-account');
    }
}