<?php

return [
   'default' => env('MAIL_MAILER', 'custom'),

    'mailers' => [
       
        'custom' => [
            'transport' => 'smtp',
            'default' => env('MAIL_MAILER', 'custom'),

            'host' => env('CUSTOM_MAIL_HOST', 'MAILSA.helb.co.ke'),
           'port' => env('CUSTOM_MAIL_PORT', 25),
            // 'encryption' => env('MAIL_ENCRYPTION', null),
         
           'username' => env('CUSTOM_MAIL_USERNAME', 'no-reply'),
           'password' => env('CUSTOM_MAIL_PASSWORD', ''),
           'timeout' => env('CUSTOM_MAIL_TIMEOUT', 60),
            'verify_peer' => false, // Temporarily disable peer verification
            'from' => [
                'address' => env('CUSTOM_MAIL_FROM_ADDRESS', 'no-reply@helb.co.ke'),
                'name' => env('CUSTOM_MAIL_FROM_NAME', 'HELB'),
            ],

        ],
    ],

   
];

