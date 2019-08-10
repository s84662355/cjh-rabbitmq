<?php
namespace CustomRabbitmq;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;

class RPCClient{
    private  $connection = null;
    private  $channel = null;
    private  $response_queue = [];

    public function __construct( AMQPStreamConnection $connection) {
    	$this->connection = $connection;
    	$this->channel = $this->connection->channel();
    }

	public function __call(string $name,array $args){
        try{
            return $this->publish($name,$args);
        }catch (AMQPConnectionClosedException $e)
        {
            throw  new \Exception($e->getMessage()) ;
        }
	}

	private function publish(string $queue,array $args)
    {
        $corr_id = uniqid();

        $callback_queue = $this->getCallBackQueue($queue);

        $msg = new AMQPMessage(
            Common::encryption($args),
            array('correlation_id' => $corr_id,
                'reply_to' => $callback_queue)
        );
        $this->channel->basic_publish($msg, '',$queue);
        $body = $this->getResponse($queue,$corr_id);

        return Common::decrypt($body);
    }

    private function getCallBackQueue($name)
    {
        $callback_queue = '';
        list($callback_queue, ,) = $this->channel->queue_declare(
                "", false, false, true, false);
        $this->response_queue[$name] = $callback_queue;
        return $this->response_queue[$name];
    }

    private function getResponse(string $queue,string $corr_id)
    {
        $callback_queue = $this->getCallBackQueue($queue);

        $response_data = false;

        $this->channel->basic_consume(
            $callback_queue, '', false, false, false, false,
            function ($response) use ( $corr_id,&$response_data) {
                if($response->get('correlation_id') == $corr_id) {
                    $response_data = $response->body;
                }
            });
        while(!$response_data) {
            $this->channel->wait();
        }

        return $response_data;
    }




}
