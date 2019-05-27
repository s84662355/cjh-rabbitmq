<?php

return [
          'server' => [
            'default' => env('RABBITMQ_MSG_DRIVER', 'default'),
            'driver' => [
                'default' => [
                    'host' => env('RABBITMQ_HOST', '127.0.0.1'),
                    'port' => env('RABBITMQ_PORT', 5672),
                    'vhost' => env('RABBITMQ_VHOST', '/'),
                    'login' => env('RABBITMQ_LOGIN', 'guest'),
                    'password' => env('RABBITMQ_PASSWORD', 'guest'),
                    'confirm_select' => true,
                ]
            ],
          ],
          'message' => [
             'default' => env('RABBITMQ_MSG_DRIVER', 'default'),
             'driver' => [
                 'default' => [
                     'durable' => true,
                     'expiration' => 0,
                     'routing_key' => '',
                     'exchange_driver' => '',
                     'queue_driver' => '',
                 ],
              ],
          ],
          'exchange'=> [
           //  'default' => env('RABBITMQ_EXCHANGE_DRIVER', 'default'),
             'driver' => [
                 'default' => [
                    'type' => 'direct',
                    'durable' => true,
                    'name' = '',
                 ],
              ],
          ],
          'queue'   => [
          //   'default' => env('RABBITMQ_QUEUE_DRIVER', 'default'),
             'driver' => [
                 'default' => [
                     'durable' => true,
                     'name' => '',
                     'routing_key' => '',
                     'exchange_driver' => '',
                     'consume' => '',
                 ],
              ],

          ],
];
