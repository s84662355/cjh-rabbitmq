<?php

namespace CustomRabbitmq;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
class Consume{

	private $callback = '';

	private $channel = null;

	private $queue = '';

	 
 
	public function __construct(AMQPChannel $channel,$queue,$callback)
	{
          $this->callback = new $callback();
          $this->channel = $channel;
          $this->queue = $queue;
          $channel->basic_qos(null, 1, null);
	}

	public function basic_consume()
	{
		$this->channel->basic_consume($this->queue, '', false, false, false, false, [$this,'process_message']);
	}

	public function process_message(AMQPMessage $msg)
	{

        $res = call_user_func_array([$this->callback,'process_message'],[new Message($msg)]);

        if($res)
        {
        	 ///出列
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        }else{
        	///重新入列
        	$message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
        }
	}
}