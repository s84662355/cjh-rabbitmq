<?php
namespace CustomRabbitmq;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;


class RabbitmqJob{
 
    private $config_pool = [];
    private $factory_pool = [];
    private $default_driver = '';
    private $select_driver = '';


	  public function __construct( $driver = false )
	  {
        $this->default_driver = config('Rabbitmq_job.default');
        $this->select_driver = $this->default_driver;
        $this->driver($driver);  
	  }

    public function select($driver = false)
    {
        $this->select_driver = $this->default_driver;
        if(!empty($driver )) $this->select_driver = $driver;
        return $this;
    }

    private function driver($driver = false)
    {
        if(!$driver)  $driver = $this->default_driver;
        if(empty($this->config_pool[$driver]))
        {
            $this->config_pool[$driver]  = config('Rabbitmq_job.driver.'.$driver);
            $this->factory_pool[$driver] = new RabbitmqDriver($this->config_pool[$driver]);
        }
        return  $this->factory_pool[$driver];
    }

    private function driver_config($driver = false)
    {
        if(!$driver)  $driver = $this->default_driver;
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
    }

    private function message($body,$rabbit_driver,$config)
    {
        if(!empty($config['exchange']))
        {
            $rabbit_driver->exchange($config['exchange']['name'], $config['exchange']['type'] ,$config['exchange']['durable']);
            $rabbit_driver->pushMessage($body,[
                    'durable' => $config['durable'],
                    'expiration' => $config['expiration'],
                    'routing_key' => $config['routing_key'],
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
      

    }



}