<?php
namespace CustomRabbitmq;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;


class CustomRabbitmqInstance{

	private $connection = null;
	private $channel = null;
	private $exchange_pool = [];
	private $queue_pool = [];
	private $publisher_instance = null;

	public function __construct( $config )
	{
		$this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
		$this->channel = $this->connection->channel(); 
		$this->publisher_instance = new Publisher($this->channel,$config['confirm_select']);
	}

    public function Exchange($name,$config)
    {
    	if(empty($exchange_pool[$name]))
    	{
    		$this->channel->exchange_declare($name,$config['type'],false,$config['durable'],false);
    		$exchange_pool[$name] = true;
    	}
        return $this;
    }

    public function Queue($name,$config)
    {
    	if(empty($queue_pool[$name]))
    	{
           $this->channel->queue_declare($name,false,$config['durable'],false,false);
           $queue_pool[$name] = true;
    	}
    	return $this;
    }

    public function QueueBind($exchange,$queue)
    {
       $channel->queue_bind($queue, $exchange);
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

    public function 
 

}