<?php

namespace App\Mail;

use App\Models\LoyaltyCoupon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoyaltyCouponMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public LoyaltyCoupon $coupon) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your ' . $this->coupon->discount_label . ' reward from ' . config('app.name', 'us'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.loyalty-coupon',
            with: [
                'coupon'         => $this->coupon,
                'ordersRequired' => (int) config('loyalty.orders_required', 5),
            ],
        );
    }
}