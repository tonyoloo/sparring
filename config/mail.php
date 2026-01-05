<?php






return [



    'default' => env('MAIL_MAILER', 'smtp'),
// 'mailers' => [      

//         'smtp' => [
//             'transport' => 'smtp',

//             'host' => env('MAIL_HOST', 'MAILSA.helb.co.ke'),
//            'port' => env('MAIL_PORT', 587),
//             // 'encryption' => env('MAIL_ENCRYPTION', null),
         
//            'username' => env('MAIL_USERNAME', 'mobileapplications@helb.co.ke'),
//            'password' => env('MAIL_PASSWORD', 'kenya+123'),
//            'timeout' => env('MAIL_TIMEOUT', 60),
//             'verify_peer' => false, // Temporarily disable peer verification
//             'from' => [
//                 'address' => env('MAIL_FROM_ADDRESS', 'mobileapplications@helb.co.ke'),
//                 'name' => env('MAIL_FROM_NAME', 'HELB'),
//             ],

//         ],

//     ],


   'mailers' => [      

        'smtp' => [
            'transport' => 'smtp',

            'host' => env('MAIL_HOST', 'MAILSA.helb.co.ke'),
           'port' => env('MAIL_PORT', 587),
            // 'encryption' => env('MAIL_ENCRYPTION', tls),
         
           'username' => env('MAIL_USERNAME', 'mobileapplications@helb.co.ke'),
           'password' => env('MAIL_PASSWORD', 'kenya+123'),
           'timeout' => env('MAIL_TIMEOUT', 60),
            'verify_peer' => false, // Temporarily disable peer verification
            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'mobileapplications@helb.co.ke'),
                'name' => env('MAIL_FROM_NAME', 'HELB'),
            ],

        ],

        'smtp_sendmail' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'mail.helb.co.ke'),
            'port' => env('MAIL_PORT',587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => '',
            'password' => '',
            'timeout' => null,
            'verify_peer' => false,
            'auth_mode' => null,
            'pretend' => false,
            // 'from' => [
            //     'address' => env('MAIL_FROM_ADDRESS', 'no-reply@helb.co.ke'),
            //     'name' => env('MAIL_FROM_NAME', 'HELB'),
            // ],

        ],

    ],





//  'mailers' => [      

//         'smtp' => [
//             'transport' => 'smtp',

//             'host' => env('MAIL_HOST', 'MAILSA.helb.co.ke'),
//            'port' => env('MAIL_PORT', 587),
//             'encryption' => env('MAIL_ENCRYPTION', 'tls'),
         
//            'username' => env('MAIL_USERNAME', 'toloo@helb.co.ke'),
//            'password' => env('MAIL_PASSWORD', 'Tronex33@'),
//            'timeout' => env('MAIL_TIMEOUT', 60),
//             'verify_peer' => false, // Temporarily disable peer verification
//             'from' => [
//                 'address' => env('MAIL_FROM_ADDRESS', 'toloo@helb.co.ke'),
//                 'name' => env('MAIL_FROM_NAME', 'HELB'),
//             ],

//         ],

//     ],








    ];