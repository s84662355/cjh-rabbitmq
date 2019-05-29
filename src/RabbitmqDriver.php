<?php
namespace CustomRabbitmq;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;


class RabbitmqDriver{

  	private $connection = null;
  	private $channel = null;
  	private $exchange_pool = [];
  	private $queue_pool = [];
  	private $publisher_instance = null;

  	public function __construct( $config )
    {
        ///
	 	  $this->connection = new AMQPStreamConnection($config['host'],$config['port'], $config['username'], $config['password'],$config['vhost']);
		  $this->channel = $this->connection->channel();
    }

    public function exchange($name,$type = 'direct',$durable = true)
    {
    	if(empty($exchange_pool[$name]))
    	{
    		$this->channel->exchange_declare($name,$type,false,$durable,false);
    		$exchange_pool[$name] = true;
    	}
      return $this;
    }

    public function queue($name,$durable = true)
    {
    	if(empty($queue_pool[$name]))
    	{
           $this->channel->queue_declare($name,false,$durable,false,false);
           $queue_pool[$name] = true;
    	}
    	return $this;
    }

    public function QueueBind($queue,$exchange,$routing_key)
    {
       $this->channel->queue_bind($queue, $exchange,$routing_key);
       return $this;
    }

    public function pushMessage($body,$config)
    {
       $this->publisher_instance->push(new Message($body,$config));
       return $this;
    }

    public function send()
    {
       $this->publisher_instance->send();
       return $this;
    }

    public function publisher($confirm_select = true) 
    {
       if($this->publisher_instance == null) $this->publisher_instance = new Publisher($this->channel,$confirm_select);
       return $this;
    }

    public function consume($queue,$consumer_tag,$callback)
    {
       return new Consume($this->channel,$queue,$consumer_tag,$callback);
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
