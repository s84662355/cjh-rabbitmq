<?php
namespace CustomRabbitmq;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Wire\AMQPTable;
 

class RabbitmqDriver{

  	private $connection = null;
  	private $channel = null;
  	private $exchange_pool = [];
  	private $queue_pool = [];
  	private $publisher_instance = null;
    private $redis = null;

    private $connection_name = '';

    private $config = null;

  	public function __construct( $config )
    {
        ///
	 	  $this->connection = new AMQPStreamConnection($config['host'],$config['port'], $config['username'], $config['password'],$config['vhost']);
          $this->connection_name = $config['host'].$config['port'].$config['vhost'];
		  $this->channel = $this->connection->channel();
		  $this->config = $config;
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

    public function cache_queue($name,$durable,$dead_ex,$dead_key,$ttl)
    {
        if(empty($queue_pool[$name]))
        {
            $tale = new AMQPTable();
            $tale->set('x-dead-letter-exchange', $dead_ex);
            $tale->set('x-dead-letter-routing-key',$dead_key);
            $tale->set('x-message-ttl',$ttl);

            $this->channel->queue_declare($name,false,$durable,false
                ,false,false,$tale);
            $queue_pool[$name] = true;
        }
        return $this;
    }

    public function queue($name,$durable = true,$ttl = 0)
    {
    	if(empty($queue_pool[$name]))
    	{
    	    if($ttl > 0){
                $tale = new AMQPTable();
                $tale->set('x-message-ttl',$ttl);
                $this->channel->queue_declare($name,false,$durable,false
                    ,false,false,$tale);
            }else{
                $this->channel->queue_declare($name,false,$durable,false
                    ,false);
            }

           $queue_pool[$name] = true;
    	}
    	return $this;
    }

    public function QueueBind($queue,$exchange,$routing_key)
    {
       $this->channel->queue_bind($queue, $exchange,$routing_key);
       return $this;
    }


    public function send($body,$config)
    {
        $this->publisher()->send(new Message($body,$config));
       return $this;
    }

    public function publisher()
    {
       if($this->publisher_instance == null) $this->publisher_instance = new Publisher($this->channel);
       return $this->publisher_instance;
    }

    public function consume($queue,$consumer_tag,$callback,$max_count = 5)
    {
       $consume = new Consume($this->channel,$queue,$consumer_tag,$callback);
       $consume-> setRedis($this->redis,$this->connection_name,$max_count );
       return $consume  ;
    }

    public function setRedis( $redis)
    {
        $this->redis = $redis;
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function refurbish()
    {
        try{
            $this->exchange_pool = [];
            $this->queue_pool = [];
            $this->publisher_instance = null;
            $this->connection->reconnect();
            $this->channel = $this->connection->channel();
        }catch (\Exception $exception){

        }
    }



}
