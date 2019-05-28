<?php

return [   
    'default' => env('RABBITMQ_MSG_DRIVER', 'default'),
    'driver' => [
        'default' => [
            'host' => env('RABBITMQ_HOST', '127.0.0.1'),
            'port' => env('RABBITMQ_PORT', 5672),
            'vhost' => env('RABBITMQ_VHOST', '/'),
            'login' => env('RABBITMQ_LOGIN', 'guest'),
            'password' => env('RABBITMQ_PASSWORD', 'guest'),
            'confirm_select' => true,
            'queue'   => [
               'default' => env('RABBITMQ_QUEUE_DRIVER', 'default'),
               'driver' => [
                   'default' => [
                       'durable' => true,
                       'name' => '',
                       'listener' => '',
                       'exchange' => [
                          'name' = '',
                          'type' => 'direct',
                          'durable' => true,
                          'routing_key' => '', 
                       ],
                   ],
                ],
            ],
        ]
    ],      
];
