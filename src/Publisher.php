<?php  

namespace CustomRabbitmq;
use PhpAmqpLib\Channel\AMQPChannel;
use CustomRabbitmq\Message;
class Publisher{

	private $confirm_select = true;

	private $msg_queue = array();

	private $channel = null;

	private static $instance = null;

	public function __construct(AMQPChannel $channel,  $confirm_select )
	{
		$this->channel = $channel;
		$this->confirm_select = $confirm_select;
	}

	public function push(Message $msg)
	{
        array_push($this->msg_queue, $msg);
	}

	public function send(Message $msg)
	{
		$this->channel->basic_publish($msg->getAmqpMsg(),$msg->getExchange(),$msg->getRoutingKey());
	}
    
    /*
	public function send()
	{
		if($this->confirm_select)  
		{
			$this->channel->confirm_select();
		}

		$array_count = count($this->msg_queue);

		for ($i=0; $i < $array_count ; $i++) { 
			$msg = array_shift($this->msg_queue);
			$this->channel->basic_publish($msg->getAmqpMsg(),$msg->getExchange(),$msg->getRoutingKey());
		}

		if($this->confirm_select)  
		{
			$this->channel->wait_for_pending_acks_returns();
		}
	}
	8/


}
