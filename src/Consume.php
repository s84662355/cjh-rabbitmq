<?php

namespace CustomRabbitmq;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;

class Consume{

	private $callback = '';
	private $channel = null;
	private $queue = '';
	private $consumer_tag = '';

	private $message_id_Arr = [];
	private $redis = null;
	private $prefix = '';
	private $max_count = 5;

	private $log_path = false;

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

    public function setLogPath($path)
    {
        $this->log_path = $path;
    }

	public function process_message(AMQPMessage $msg)
	{
		$res = AbstractConsume::ACK;

		$log_str = '';

        try{
            $body = $msg->getBody();
            $body = json_decode($body,true);
            $body_data =  base64_decode($body['body']);
            $log_str.= $body_data;

            if(!empty( $body['message_id'] ) && !empty($this->redis))
            {
                $message_key = $this->prefix.$body['message_id'];

                if( isset($this->callback->tries) )
                {
                    $this->max_count = $this->callback->tries;
                }

                if($this->redis->incrby($message_key,1) > $this->max_count )
                {
                    $this->redis->del($message_key);//true
                    return   $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                }
                $this->redis->expire($message_key, 200);
                $this->callback->message_id = $body['message_id'];

            }

            $res = call_user_func_array([$this->callback,'process_message'],[ $body_data,$body['config']]);
            if(empty($res))
                $res = AbstractConsume::REJECT_OUT;

            $log_str.= '   return '.$res;




        }catch (\Exception $e)
        {
            $log_str.=$this->ErrorLog($e);
            $res = AbstractConsume::REJECT;
        }catch (\ErrorException $errorException)
        {
            $log_str.=$this->ErrorLog($errorException);
            $res = AbstractConsume::REJECT;
        }catch (\ParseError $parseError)
        {
            $log_str.=$this->ErrorLog($parseError);
            $res = AbstractConsume::REJECT;
        }catch (\TypeError $typeError)
        {
            $log_str.=$this->ErrorLog($typeError);
            $res = AbstractConsume::REJECT;
        }




        ///   basicAck：成功消费，消息从队列中删除
        //   basicNack：requeue=true，消息重新进入队列，false被删除
        //   basicReject：等同于basicNack
        //   basicRecover：消息重入队列，requeue=true，发送给新的consumer，false发送给相同的consumer

        if($res == AbstractConsume::ACK)
        {
            ///出列
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        }else if($res == AbstractConsume::REJECT){
            //拒绝 回列
            $msg->delivery_info['channel']->basic_reject($msg->delivery_info['delivery_tag'],true);
        }else if($res == AbstractConsume::CANCEL){
            $msg->delivery_info['channel']->basic_cancel($msg->delivery_info['consumer_tag']);
        }else if($res == AbstractConsume::REJECT_OUT){
            //拒绝 并且出列
            $msg->delivery_info['channel']->basic_reject($msg->delivery_info['delivery_tag'],false);
        }

        if($this->log_path)
        {
            LogService::instance($this->log_path)->info($log_str);
        }

        if($res == AbstractConsume::ACK || $res == AbstractConsume::REJECT_OUT )
        {

        }

	}


	private function ErrorLog($e)
    {
        $log_str = '      ';

      ///  $log_str.=$e->getTraceAsString();
        $log_str.= get_class($e);
        $log_str.="    ";
        $log_str.=$e->getFile();
        $log_str.="  line:";
        $log_str.=$e->getLine();
        $log_str.="    ";
        $log_str.=$e->getMessage();
        $log_str.="    ";
        return $log_str;
    }




}
