<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage)
    {
        //
    }

    public function build()
    {
        return $this
            ->subject('Re: ' . $this->contactMessage->subject)
            ->view('emails.contact.reply')
            ->with(['contactMessage' => $this->contactMessage]);
    }
}