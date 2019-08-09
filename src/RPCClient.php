<?php
namespace CustomRabbitmq;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
class RPCClient{
    private  $connection = null;
    private  $channel = null;
    private  $request_queue = [];
    private  $response_queue = [];

    public function __construct( AMQPStreamConnection $connection) {
    	$this->connection = $connection;
    	$this->channel = $this->connection->channel();
        list($this->callback_queue, ,) = $this->channel->queue_declare(
            "", false, false, true, false);
    }


    public function on_response($rep) {
        if($rep->get('correlation_id') == $this->corr_id) {
            $this->response = $rep->body;
        }
    }

	public function __call($name,$args) {  


         
		$this->channel->basic_publish(json_encode($args), '', $name);

	    
	}  




}
