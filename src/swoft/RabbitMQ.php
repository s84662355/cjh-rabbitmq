<?php declare(strict_types=1);


namespace CustomRabbitmq\swoft;


use function bean;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Stdlib\Helper\Arr;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use CustomRabbitmq\RabbitmqDriver;

/**
 * Class RabbitMQ
 *
 * @since 2.0
 */
class RabbitMQ
{


    /**
     * Set rabbitmq config
     *
     * @var array
     *
     */
    private $config = [
        'host'     => '127.0.0.1',
        'port'     => 5672,
        'vhost'    => '/',
        'username' => 'guest',
        'password' => 'guest',
    ];

    /**
     * Set client publish.
     *
     * @var array
     *
     */
    private $publish = [];

    /**
     * Set RabbitMQ  connection
     *
     * @var AMQPStreamConnection
     *
     */
    private $connection = null;

    public function getConnection()
    {
        if(empty($this->connection))
            $this->connection = new AMQPStreamConnection(
                $this->config['host'],
                $this->config['port'],
                $this->config['username'],
                $this->config['password'],
                $this->config['vhost']
            );
        return $this->connection;
    }

    public function createChannel()
    {
        return   new RabbitMQChannel($this);
    }

}
