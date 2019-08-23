<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: chenjiahao
 * Date: 2019-08-16
 * Time: 12:03
 */

namespace CustomRabbitmq\swoft;

use ReflectionException;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Connection\Pool\AbstractPool;
use Swoft\Connection\Pool\Contract\ConnectionInterface;

use Throwable;

class RabbitMQChannelPool  extends AbstractPool
{

    /**
     * @var RabbitMQ
     */
    protected $rabbitMq;


    /**
     * @return ConnectionInterface
     *
     *
     *
     */
    public function createConnection(): ConnectionInterface
    {
        return $this->rabbitMq->createChannel();
    }






}