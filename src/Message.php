<?php
namespace CustomRabbitmq;
use PhpAmqpLib\Message\AMQPMessage;

class Message{

	private  $durable = true;
	private  $amqp_msg= null;
    private  $body = [];
    private  $routing_key = '';
    private  $exchange = '';
    private  $queue = '';
    private  $config = [
        'content_type' => 'text/plain',
        'delivery_mode'=> AMQPMessage::DELIVERY_MODE_PERSISTENT,
        'message_id' => "1234567890",
    ];

    /*
     expiration
    */
	public function __construct($body,array $config = [])
	{
		$this->body = $body;

        $this->iniConfig( $config );

        $data = [
           'body' => $body,
           'config' => $config
        ];
 
		$this->amqp_msg = new AMQPMessage(json_encode($data),$this->config);
	}

    private function iniConfig(array $config = [])
    {
        if(empty($config['durable'])){
            $this->durable = false;
            $this->config['delivery_mode'] = AMQPMessage::DELIVERY_MODE_NON_PERSISTENT;
        }

        if(!empty($config['expiration'])){
              $this->config['expiration'] = $config['expiration'];
        }


        if(!empty($config['exchange'])){
            $this->exchange = $config['exchange'];
        }


        if(!empty($config['routing_key'])){
            $this->routing_key = $config['routing_key'];
        } 

        if(!empty($config['queue'])){
            $this->queue = $config['queue'];
        } 

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
        return !empty($this->routing_key) ?  $this->routing_key : $this->queue   ;
    }

    public function getExchange()
    {
    	return $this->exchange;
    }


    /*
    public function setRoutingKey($routing_key = '')
    {
    	$this->routing_key = $routing_key;
    }

    public function setExchange($exchange = '')
    {
    	$this->exchange = $exchange;
    }

    */
    

	/*

	public function __get($key)
	{
		if(isset($this->$key)){
			return $this->$key;
		}
		return null;
	}*/



}
