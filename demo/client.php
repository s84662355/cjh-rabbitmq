<?php
/**
 * Created by PhpStorm.
 * User: chenjiahao
 * Date: 2019-09-11
 * Time: 15:23
 */

require_once  dirname(__DIR__ ). '/vendor/autoload.php';
use CustomRabbitmq\MQJob;
use CustomRabbitmq\AbstractConsume;

$config = [
    'default' =>   'first' ,
    'driver' => [
        'first' => [
            'host' =>  '47.112.128.19',
            'port' =>   5672 ,
            'vhost' =>   '/' ,
            'username' =>   'guest' ,
            'password' =>  '123456' ,


            'publish' => [
                'default' =>  '1',
                'driver' => [
                    '1' => [
                        'durable' => true,
                        'delayed' => true,
                        'queue' => [
                            'durable' => true,
                            'name' => 'aaaaa423',
                        ]
                    ],


                    '2' => [
                        'durable' => true,
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

                    'delayed' => [
                        'delayed' => true,
                        'durable' => true,
                        'queue' => [
                            'durable' => true,
                            'name' => 'delayed',
                        ]
                    ],


                ],

            ],

            'consume' => [
                'default' =>   'first' ,
                'driver' => [
                    'first' => [
                        'max_count' => 5,
                        'durable' => true,
                        'consumer_tag' => '1322423',
                        'queue' => 'aaaaa423',
                        'timedelay'  => 5000,
                        'listener' => 'Test',
                        /// 'log_path' =>storage_path("logs/"  . "TestConsume.log"),
                        'arguments' => [
                            //  'x-message-ttl' => 100000,

                            'x-max-length'  => 10000,
                        ],


                    ],
                ],
            ],
        ]
    ],
];



$job = new MQJob($config );

$job->send('ssdsdsdsdsdss');