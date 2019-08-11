<?php
namespace CustomRabbitmq;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;


class RabbitmqJob{
 
    private $config_pool = [];
    private $factory_pool = [];
    private $default_driver = '';
    private $select_driver = '';
    private $config = [];
    private $redis = null;

    public function __construct($config , $driver = false  )
    {
        $this->config = $config;
        $this->default_driver = $config['default'];//config('rabbitmq_job.driver.default');
        $this->select_driver = $this->default_driver;
     ////   $this->driver();
    }

    public function setRedis($redis)
    {
        $this->redis = $redis;
    }

    ###切换连接
    public function select($driver = false)
    {
        $this->select_driver = $this->default_driver;
        if(!empty($driver )) $this->select_driver = $driver;
        $this->driver();
        return $this;
    }

    private function driver($driver = false)
    {
        if(!$driver)  $driver = $this->select_driver;
        if(empty($this->config_pool[$driver]))
        {
            $this->config_pool[$driver]  = $this->config['driver'][$driver];  ;//config('rabbitmq_job.driver.'.$driver);
            $this->factory_pool[$driver] = new RabbitmqDriver($this->config_pool[$driver]);
            $this->factory_pool[$driver]->setRedis($this->redis);
        }
        return  $this->factory_pool[$driver];
    }

    private function driver_config($driver = false)
    {
        if(!$driver) $driver = $this->select_driver;
        if(empty($this->config_pool[$driver]))$rabbit_driver =  $this->driver();
        return $this->config_pool[$driver];
    }



    public function send($body,$msg_driver_name = false)
    {
        $rabbit_driver =  $this->driver();

        $driver_config = $this->driver_config( );
     
        if(!$msg_driver_name)  $msg_driver_name = $driver_config['publish']['default'];
        $msg_driver = $driver_config['publish']['driver'];
        $this->message($body,$rabbit_driver,$msg_driver[$msg_driver_name]);

        return $this;
    }

    
    public function tx_select()
    {
        $this->driver()->tx_select();
    }


    public function tx_commit()
    {
        $this->driver()->tx_commit();
    }


    public function tx_rollback()
    {
        $this->driver()->tx_rollback();
    }


    private function message($body,$rabbit_driver,$config)
    {
        if(!empty($config['timedelay']) && $config['timedelay'] > 0  )
        {
            $rabbit_driver->exchange("dead-exchange", 'direct' ,true);
            $rabbit_driver->cache_queue('cache_'.$config['queue']['name'],$config['queue']['durable'],"dead-exchange",'dead_'.$config['queue']['name'].'_key',$config['timedelay']);


            $rabbit_driver->queue($config['queue']['name'],$config['queue']['durable'],$config['expiration']);


            $rabbit_driver->QueueBind($config['queue']['name'],"dead-exchange",'dead_'.$config['queue']['name'].'_key');

            $rabbit_driver->send($body,[
                'durable' => $config['durable'],
                'expiration' => $config['timedelay'],
                'queue' => 'cache_'.$config['queue']['name']
            ]);

            return $this;
        }


        if(!empty($config['exchange']))
        {

            $rabbit_driver->exchange($config['exchange']['name'], $config['exchange']['type'] ,$config['exchange']['durable']);

            /*
            $rabbit_driver->pushMessage($body,[
                    'durable' => $config['durable'],
                    'expiration' => $config['expiration'],
                    'routing_key' => $config['exchange']['routing_key'],
                    'exchange' => $config['exchange']['name'],
            ]);
            */

            $rabbit_driver->send($body,[
                'durable' => $config['durable'],
                'expiration' => $config['expiration'],
                'routing_key' => $config['exchange']['routing_key'],
                'exchange' => $config['exchange']['name'],
            ]);

            return $this;
        }

        if(!empty($config['queue']))
        {
            $rabbit_driver->queue($config['queue']['name'],$config['queue']['durable']);
            $rabbit_driver->send($body,[
                'durable' => $config['durable'],
                'expiration' => $config['expiration'],
                'queue' => $config['queue']['name'],
            ]);

            /*
            $rabbit_driver->pushMessage($body,[
                    'durable' => $config['durable'],
                    'expiration' => $config['expiration'],
                    'queue' => $config['queue']['name'],
            ]);
            */
        }
        return $this;
    }

    public function consume($consume_driver = false)
    {
        $rabbit_driver = $this->driver();

        $driver_config  = $this->driver_config();
        if(!$consume_driver)  $consume_driver = $driver_config['consume']['default'];
        $consume_driver = $driver_config['consume']['driver'][$consume_driver];

        $rabbit_driver->queue($consume_driver['queue'],$consume_driver['durable']);
        if(!empty($consume_driver['exchange']))
        {
            $exchange = $consume_driver['exchange'];
            $rabbit_driver->exchange($exchange['name'], $exchange['type'] ,$exchange['durable'])
                          ->QueueBind($consume_driver['queue'],$exchange['name'],$exchange['routing_key']);

                          //->consume($consume_driver['queue'],$consume_driver['consumer_tag'],$consume_driver['listener'])
                         // ->basic_consume();
        }
        $max_count = 5;
        if(!empty($consume_driver['max_count'] )) 
            $max_count = $consume_driver['max_count'];

        $consume = $rabbit_driver->consume($consume_driver['queue'],$consume_driver['consumer_tag'],$consume_driver['listener'],$max_count );

        if(isset($consume_driver['log_path']))
            $consume->setLogPath($consume_driver['log_path']);

        $consume ->basic_consume();

    }



}
