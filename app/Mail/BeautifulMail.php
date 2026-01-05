<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class BeautifulMail extends Mailable
{
    use Queueable, SerializesModels;
    public $notifydets;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($notifydets)
    {

        $this->salutation = $notifydets->salutation;
        $this->emailmessage = $notifydets->emailmessage;
        $this->subject = $notifydets->subject;

        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $msg = [


            'salutation'  => $this->salutation,
            'emailmessage'  =>  $this->emailmessage
        ];

         // Dynamically load the custom mail configuration if needed
       

        return $this
            ->subject($this->subject)

            ->markdown('beautifulmail')


            ->with('msg', $msg);
    }
}
