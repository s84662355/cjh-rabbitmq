<?php

namespace CustomRabbitmq;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
class Consume{

	private $callback = '';
	private $channel = null;
	private $queue = '';
	private $consumer_tag = '';
 
	public function __construct(AMQPChannel $channel,$queue,$consumer_tag,$callback)
	{
          $this->callback = new $callback();
          $this->channel = $channel;
          $this->queue = $queue;
          $this->consumer_tag = $consumer_tag;
          $channel->basic_qos(null, 1, null);
	}

	public function basic_consume()
	{
		$this->channel->basic_consume($this->queue, $this->consumer_tag, false, false, false, false, [$this,'process_message']);
        while($this->channel->is_consuming()) {
            $this->channel->wait();
        }
	}

	public function process_message(AMQPMessage $msg)
	{
        $res = call_user_func_array([$this->callback,'process_message'],[$msg->getBody()]);

        if($res == AbstractConsume::ACK)
        {
           ///出列
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        }else if($res == AbstractConsume::REJECT){
            $msg->delivery_info['channel']->basic_reject($msg->delivery_info['delivery_tag'],true);
        }else if($res == AbstractConsume::CANCEL){
            $msg->delivery_info['channel']->basic_cancel($msg->delivery_info['consumer_tag']);
        }   
	}
}
