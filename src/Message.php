<?php
namespace CustomRabbitmq;
use PhpAmqpLib\Message\AMQPMessage;

class Message{

	private  $durable = true;
	private  $amqp_msg= null;
    private  $body = '';
    private  $routing_key = '';
    private  $exchange = '';
    private  $config = [
      'content_type' => 'text/plain',
      'delivery_mode'=> AMQPMessage::DELIVERY_MODE_PERSISTENT
    ];
 
    /*
     expiration
    */
	public function __construct(string $body,array $config = [])
	{
		$this->body = $body;

		if(empty($config['durable'])){
			$this->durable = false;
			$this->config['delivery_mode'] = AMQPMessage::DELIVERY_MODE_NON_PERSISTENT;
		}

		if(!empty($config['exchange'])){
			$this->exchange = $config['exchange'];
		}

		if(!empty($config['expiration'])){
              $this->config['expiration'] = $config['expiration'];
		}

		if(!empty($config['routing_key'])){
			$this->routing_key = $config['routing_key'];
		}
 
		$this->amqp_msg = new AMQPMessage($body,$this->config);
	}

	public function getDurable()
	{
		return $this->durable;
	} 

    public function getAmqpMsg()
    {
    	return $this->amqp_msg;
    }

    public function getBody()
    {
    	return $this->body;
    }
    
    public function getRoutingKey()
    {
        return $this->routing_key;
    }

    public function getExchange()
    {
    	return $this->exchange;
    }

    public function setRoutingKey($routing_key = '')
    {
    	$this->routing_key = $routing_key;
    }

    public function setExchange($exchange = '')
    {
    	$this->exchange = $exchange;
    }
    

	/*

	public function __get($key)
	{
		if(isset($this->$key)){
			return $this->$key;
		}
		return null;
	}*/



}
