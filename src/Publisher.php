<?php  

namespace CustomRabbitmq;
use PhpAmqpLib\Channel\AMQPChannel;
use CustomRabbitmq\Message;
use PhpAmqpLib\Message\AMQPMessage;
class Publisher{

	private $confirm = true;

	///private $msg_queue = array();

	private $channel = null;

	private static $instance = null;

	private $confirm_ask = true;

	public function __construct( AMQPChannel $channel , $confirm = true )
	{
		$this->channel = $channel;

        $this->confirm = $confirm;

		if($confirm)
        {
            $this->channel->confirm_select();
            $this->setHandler();
        }

	}

	public function setHandler()
    {
        $this->channel->set_ack_handler([$this,'ack_handler']);

        $this->channel->set_nack_handler([$this,'nack_handler']);
    }


    public function ack_handler(AMQPMessage $message)
    {
        $this->confirm_ask = true;
    }

    public function nack_handler(AMQPMessage $message)
    {
        $this->confirm_ask = false;
    }




    /*
    public function push(Message $msg)
    {
        array_push($this->msg_queue, $msg);
    }
    */

	public function send(Message $msg)
    {
        if($this->confirm)
        {
            $this->channel->basic_publish($msg->getAmqpMsg(), $msg->getExchange(), $msg->getRoutingKey());
            $this->channel->wait_for_pending_acks();
            if($this->confirm_ask == false)
                throw new \Exception('rabbitmq confirm 失败');

        }else{
            $this->channel->basic_publish($msg->getAmqpMsg(), $msg->getExchange(), $msg->getRoutingKey());
        }
    }




}
