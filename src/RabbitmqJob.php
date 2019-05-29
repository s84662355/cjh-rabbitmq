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


    public function __construct($config , $driver = false  )
    {
        $this->config = $config;
        $this->default_driver = $config['default'];//config('rabbitmq_job.driver.default');
        $this->select_driver = $this->default_driver;
        $this->driver();
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
        }
        return  $this->factory_pool[$driver];
    }

    private function driver_config($driver = false)
    {
        if(!$driver) $driver = $this->select_driver;
        return $this->config_pool[$driver];
    }

    public function publisher($callback )
    {
        $driver = $this->select_driver ;
        $driver_config = $this->driver_config($driver);
        $msg_driver = $driver_config['publish']['driver'];
        $msg_default_driver = $driver_config['publish']['default'];
        $job_publisher = new JobPublisher($msg_driver , $msg_default_driver);
        $callback($job_publisher);
        $msg_queue = $job_publisher->getQueue();
        $rabbit_driver = $this->driver($driver);
        $rabbit_driver->publisher($driver_config ['confirm_select'] ) ;
        foreach ($msg_queue as $key => $value) {
            $this->message($value['body'],$rabbit_driver,$msg_driver[$value['driver']]);
        }
        $rabbit_driver->send();
        return $this;
    }

    private function message($body,$rabbit_driver,$config)
    {
        if(!empty($config['exchange']))
        {
            $rabbit_driver->exchange($config['exchange']['name'], $config['exchange']['type'] ,$config['exchange']['durable']);
            $rabbit_driver->pushMessage($body,[
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
            $rabbit_driver->pushMessage($body,[
                    'durable' => $config['durable'],
                    'expiration' => $config['expiration'],
                    'queue' => $config['queue']['name'],
            ]);

        }
        return $this;
    }

    public function consume($consume_driver = false)
    {
        $driver_config  = $this->driver_config();
        if(!$consume_driver)  $consume_driver = $driver_config['consume']['default'];
        $consume_driver = $driver_config['consume']['driver'][$consume_driver];
        $rabbit_driver = $this->driver();
        $rabbit_driver->queue($consume_driver['queue'],$consume_driver['durable']);
        if(!empty($consume_driver['exchange']))
        {
            $exchange = $consume_driver['exchange'];
            $rabbit_driver->exchange($exchange['name'], $exchange['type'] ,$exchange['durable'])
                          ->QueueBind($consume_driver['queue'],$exchange['name'],$exchange['routing_ke']);

                          //->consume($consume_driver['queue'],$consume_driver['consumer_tag'],$consume_driver['listener'])
                         // ->basic_consume();
        }

        $rabbit_driver->consume($consume_driver['queue'],$consume_driver['consumer_tag'],$consume_driver['listener'])->basic_consume();

    }



}
