<?php

return [
      'default' => env('RABBITMQ_MSG_DRIVER', 'first'),
      'driver' => [
          'first' => [
              'host' => env('RABBITMQ_HOST', '127.0.0.1'),
              'port' => env('RABBITMQ_PORT', 5672),
              'vhost' => env('RABBITMQ_VHOST', '/'),
              'login' => env('RABBITMQ_LOGIN', 'guest'),
              'password' => env('RABBITMQ_PASSWORD', 'guest'),
              'confirm_select' => true,

              'publish' => [
                 'default' => env('RABBITMQ_MSG_DRIVER', 'first'),
                 'driver' => [
                     'first' => [
                         'durable' => true,
                         'expiration' => 0,
                         'exchange' => [
                            'name' = '',
                            'type' => 'direct',
                            'durable' => true,
                            'routing_key' => '', 
                         ],
                         'queue' => [
                            'durable' => true,
                            'name' => '',
                         ]
                     ],
                  ],
              ],

              'consume' => [
                   'default' => env('RABBITMQ_QUEUE_DRIVER', 'first'),
                   'driver' => [
                       'first' => [
                           'durable' => true,
                           'queue' => '',
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
