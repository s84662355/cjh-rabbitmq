<?php
namespace CustomRabbitmq;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;


class JobPublisher{

    private $msg_queue = array();
    private $driver_array = [];
    private $default_driver = '';

    public function __construct( array $driver_array , string $default_driver )
    {
        $this->driver_array = $driver_array;
        $this->default_driver = $default_driver;
    }

    public function push($body,$driver = false)
    {
        if(!$driver) $driver = $this->default_driver;
        ###以后抛出错误
        if(empty($this->driver_array[$driver])) throw new \Exception("消息驱动不存在");
        $this->msg_queue[] = [
             'driver' => $driver ,
             'body' => $body,
        ];
    }

    public function getQueue()
    {
        return $this->msg_queue;
    } 

    public function empty()
    {
        $this->msg_queue = [];
        return $this;
    }
}
