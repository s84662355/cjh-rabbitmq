<?php
/**
 * Created by PhpStorm.
 * User: chenjiahao
 * Date: 2019-08-23
 * Time: 16:35
 */

namespace CustomRabbitmq;
use PhpAmqpLib\Channel\AMQPChannel;
use CustomRabbitmq\Message;
use PhpAmqpLib\Message\AMQPMessage;

class TxPublisher
{

    private $channel = null;



    public function __construct( AMQPChannel $channel  )
    {
        $this->channel = $channel;
    }



    public function send(Message $msg)
    {
        $this->channel->basic_publish($msg->getAmqpMsg(), $msg->getExchange(), $msg->getRoutingKey());
    }




}