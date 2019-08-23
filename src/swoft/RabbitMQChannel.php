<?php declare(strict_types=1);

namespace CustomRabbitmq\swoft;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Wire\AMQPTable;
use CustomRabbitmq\swoft\RabbitMQ as RabbitmqDriver;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Swoft\Bean\BeanFactory;

class RabbitMQChannel extends AbstractRabbitMQChannel
{




    /**
     * Set rabbitmq channel
     *
     * @var AMQPChannel
     *
     */
    private $channel = null;

    /**
     * Set rabbitmq driver
     *
     * @var RabbitmqDriver
     *
     */
    private $rabbitmq_driver = null;

    public function __construct(RabbitmqDriver  $rabbitmq_driver )
    {
        $this->rabbitmq_driver = $rabbitmq_driver;

    }

    /**
     *
     *
     *
     */
    public function create(): void
    {
       $this->getChannel();
    }


    /**
     * Close connection
     */
    public function close(): void
    {
        $this->channel->close();
    }


    public function getChannel()
    {
        if(empty($this->channel)) $this->channel = $this->rabbitmq_driver->createChannel();
        return $this->channel;
    }


    /**
     * @param bool $force
     *

     *
     */
    public function release(bool $force = false): void
    {
        /* @var ChannelManager $channelManager */
        $channelManager = BeanFactory::getBean(ChannelManager::class);
        $channelManager->releaseChannel($this->rabbitmq_driver->);

        parent::release($force);
    }



}