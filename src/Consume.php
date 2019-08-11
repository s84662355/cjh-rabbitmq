<?php

namespace CustomRabbitmq;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
 
class Consume{

	private $callback = '';
	private $channel = null;
	private $queue = '';
	private $consumer_tag = '';

	private $message_id_Arr = [];
	private $redis = null;
	private $prefix = '';
	private $max_count = 5;

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

    public function setRedis(  $redis,$prefix = '',$max_count = 5)
    {
        $this->redis = $redis;
        $this->prefix = $prefix;
        $this->max_count = $max_count;
    }



	public function process_message(AMQPMessage $msg)
	{
		$res = AbstractConsume::ACK;
        try{

            $body = $msg->getBody();
            $body = json_decode($body,true);


            if(!empty( $body['message_id']) && !empty($this->redis))
            {

                $message_key = $this->prefix.$body['message_id'];

                if($this->redis->incrby($message_key,1) > $this->max_count )
                {
                    $this->redis->del($message_key);//true


                    return   $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                }

                $this->redis->expire($message_key, 200);
            }

            $res = call_user_func_array([$this->callback,'process_message'],[base64_decode($body['body']),$body['config']]);
        }catch (\Exception $e)
        {
            $res = AbstractConsume::REJECT;
        }catch (\Throwable $throwable)
        {
            $res = AbstractConsume::REJECT;
        }catch (\ParseError $parseError)
        {
            $res = AbstractConsume::REJECT;
        }catch (\TypeError $typeError)
        {
            $res = AbstractConsume::REJECT;
        }


        if($res == AbstractConsume::ACK)
        {
           ///出列
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        }else if($res == AbstractConsume::REJECT){

        }else if($res == AbstractConsume::CANCEL){
            $msg->delivery_info['channel']->basic_cancel($msg->delivery_info['consumer_tag']);
        }

	}
}
