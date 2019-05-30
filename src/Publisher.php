<?php  

namespace CustomRabbitmq;
use PhpAmqpLib\Channel\AMQPChannel;
use CustomRabbitmq\Message;
class Publisher{

	private $confirm_select = true;

	///private $msg_queue = array();

	private $channel = null;

	private static $instance = null;

	public function __construct(AMQPChannel $channel  )
	{
		$this->channel = $channel;
	}

	/*
	public function push(Message $msg)
	{
        array_push($this->msg_queue, $msg);
	}
	*/

	public function send(Message $msg)
    {
       $this->channel->basic_publish($msg->getAmqpMsg(), $msg->getExchange(), $msg->getRoutingKey());
    }




}
