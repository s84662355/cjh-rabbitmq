<?php declare(strict_types=1);

namespace CustomRabbitmq\swoft;

use function count;
use Redis;
use RedisCluster;
use ReflectionException;
use function sprintf;
use Swoft;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Connection\Pool\AbstractConnection;
use Swoft\Log\Helper\Log;
use Swoft\Stdlib\Helper\PhpHelper;
use Throwable;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Wire\AMQPTable;

class AbstractRabbitMQChannel extends AbstractConnection
{

}