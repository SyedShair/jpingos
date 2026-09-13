<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        // Items are needed in the view; load them here so the
        // controller doesn't have to remember to eager-load before
        // handing the order off to this mailable.
        $this->order->loadMissing('items');
    }

    public function build()
    {
        return $this
            ->subject('Order Confirmed — ' . $this->order->order_number)
            ->view('storefront.emails.orders.confirmed')
            ->with(['order' => $this->order]);
    }
}