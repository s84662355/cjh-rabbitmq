<?php
namespace CustomRabbitmq;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use PhpAmqpLib\Channel\AbstractChannel as Channel;

class RPCClient {
    private $channel;
    private $callback_queue;
    private $response;
    private $corr_id;
    private $request_queue;


    public function __construct(Channel $channel,string $request_queue,string $connect_name = null) {
        $this->channel = $channel;
        $this->request_queue = $request_queue;
        list($this->callback_queue, ,) = $this->channel->queue_declare(
            "", false, false, true, false);
        $this->channel->basic_consume(
            $this->callback_queue, '', false, false, false, false,
            array($this, 'on_response'));
        $this->corr_id = md5($connect_name.time().uniqid().rand(1000,100000).implode('',$_ENV));
    }


    public function on_response($rep) {
        if($rep->get('correlation_id') == $this->corr_id) {
            $this->response = $rep->body;
        }
    }

    public function call($n,$method) {
        $this->response = null;
        $this->corr_id = uniqid();

        $msg = new AMQPMessage(
            (string) $n,
            array('correlation_id' => $this->corr_id,
                'request_method' => $method,
                'reply_to' => $this->callback_queue)
        );
        $this->channel->basic_publish($msg, '', $this->request_queue);
        while(!$this->response) {
            $this->channel->wait();
        }
        return  $this->response;
    }
};
