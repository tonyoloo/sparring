<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class ApplicationCountMail extends Mailable
{
    use Queueable, SerializesModels;
    public $notifydets;
    public $tableHtml;
    public $ccAddresses;
    public $emailmessage;
    public $salutation;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($notifydets, $tableHtml,$ccAddresses)
    {

        $this->salutation = $notifydets->salutation;
        $this->emailmessage = $notifydets->emailmessage;
        $this->subject = $notifydets->subject;
        $this->tableHtml = $tableHtml;
        $this->ccAddresses = $ccAddresses;

       // dd()


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
            'emailmessage'  =>  $this->emailmessage,
            'tableHtml' => $this->tableHtml

        ];

         // Dynamically load the custom mail configuration if needed
       
         //$ccAddresses = ['tonyoloo15@gmail.com', 'tonyoloo@ymail.com']; // Add your CC addresses here

        return $this
            // ->theme('custom')

            ->subject($this->subject)
            ->cc($this->ccAddresses) // Adding CC recipients


            ->markdown('beautifulmail')


            ->with('msg', $msg);
    }
}
