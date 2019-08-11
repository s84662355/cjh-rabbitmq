<?php
namespace CustomRabbitmq;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;

class RPCServer{
    private  $connection = null;
    private  $channel = null;
    private  $rpc_queue = '';
    private  $rpc_server = null;

    public function __construct( AMQPStreamConnection $connection) {
    	$this->connection = $connection;
    	$this->channel = $this->connection->channel();
        $this->channel->basic_qos(null, 1, null);
    }



    public function begin()
    {
        $this->channel->basic_consume($this->rpc_queue, '', false, false, false, false, [$this,'call_back']);
        while(count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    public function call_back($request)
    {
         $body = $request->body;
         $response = $this->serverConsume->handle( Common::decrypt($body) );

    }
}
