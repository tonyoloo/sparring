<?php






return [

    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all e-mails sent by your application to be sent from
    | the same address. Here, you may specify a name and address that is
    | used globally for all e-mails that are sent by your application.
    |
    */

    // 'from' => [
    //     'address' => 'noreply@topi.co.ke',
    //     'name' => 'Ngumi Network',
    // ],

     'from' => [
        'address' => 'noreply@nguminetwork.co.ke',
        'name' => 'Ngumi Network',
    ],




   'mailers' => [

        // 'smtp' => [
        //     'transport' => 'smtp',
        //     'host' => 'mail.topi.co.ke',
        //     'port' => 587,
        //     'encryption' => 'tls',
        //     'username' => 'noreply@topi.co.ke',
        //     'password' => 'Tronex29@',
        //     'timeout' => env('MAIL_TIMEOUT', 60),
        //     'verify_peer' => false,
        // ],

 'smtp' => [
            'transport' => 'smtp',
            'host' => 'mail.nguminetwork.co.ke',
            'port' => 587,
            'encryption' => 'tls',
            'username' => 'noreply@nguminetwork.co.ke',
            'password' => 'Tronex29@',
            'timeout' => env('MAIL_TIMEOUT', 60),
            'verify_peer' => false,
        ],


      

    ],












    ];