<?php

return [
      'default' => env('RABBITMQ_MSG_DRIVER', 'first'),
      'driver' => [
          'first' => [
              'host' =>  env('RABBITMQ_HOST', '127.0.0.1'),
              'port' =>  env('RABBITMQ_PORT', 5672),
              'vhost' => env('RABBITMQ_VHOST', '/'),
              'username' => env('RABBITMQ_LOGIN', 'guest'),
              'password' => env('RABBITMQ_PASSWORD', 'guest'),


              'publish' => [
                 'default' => env('RABBITMQ_MSG_DRIVER', '1'),
                 'driver' => [
                     '1' => [
                         'durable' => true,
                         'expiration' => 0,
                          /*
                         'exchange' =>
                             [
                            'name' => '1111',
                            'type' => 'direct',
                            'durable' => true,
                            'routing_key' => '1111',
                         ],
                          */
                         'queue' => [
                            'durable' => true,
                            'name' => '1322423',
                         ]
                     ],
                     '2' => [
                         'durable' => true,
                         'expiration' => 0,
                         'exchange' => [
                             'name' => '22222',
                             'type' => 'direct',
                             'durable' => true,
                             'routing_key' => '2222',
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
                           'consumer_tag' => '1322423',
                           'queue' => '1322423',
                           'listener' => 'App\TestConsume',
                           'exchange' => [
                              'name'  => '1111',
                              'type'  => 'direct',
                              'durable' => true,
                              'routing_key' => '1111',
                           ],

                        ],
                    ],
              ],
          ]
      ],
];
