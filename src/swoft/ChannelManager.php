<?php declare(strict_types=1);

namespace CustomRabbitmq\swoft;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Co;
use Swoft\Concern\ArrayPropertyTrait;
use Swoft\Connection\Pool\Contract\ConnectionInterface;
use CustomRabbitmq\swoft\AbstractRabbitMQChannel;

/**
 * Class ConnectionManager
 *
 * @since 2.0
 *
 * @Bean()
 */
class  ChannelManager
{
    /**
     * @example
     * [
     *     'tid' => [
     *         'cid' => [
     *             'connectionId' => Connection
     *         ]
     *     ]
     * ]
     */
    use ArrayPropertyTrait;

    /**
     * @param AbstractRabbitMQChannel $channel
     * @param istring $rabbitmq_connection
     */
    public function setChannel(AbstractRabbitMQChannel $channel,string $rabbitmq_connection ): void
    {
        ///$key = sprintf('%d.%d.%d', Co::tid(), Co::id(),$channel->getId());
        $key = sprintf('%d.%d.%d', Co::tid(), Co::id(), $rabbitmq_connection );
        $this->set($key, $channel);
    }

    /**
     * @param istring $rabbitmq_connection
     */
    public function releaseChannel(string $rabbitmq_connection): void
    {
        ///$key = sprintf('%d.%d.%d', Co::tid(), Co::id(), $id);

        $key = sprintf('%d.%d.%d', Co::tid(), Co::id(), $rabbitmq_connection);
        $this->unset($key);
    }

    /**
     * @param bool $final
     */
    public function release(bool $final = false): void
    {
        $key = sprintf('%d.%d', Co::tid(), Co::id());

        $channels = $this->get($key, []);
        foreach ($channels as $channel) {
            if ( $channel instanceof AbstractRabbitMQChannel) {
                $channel->release();
            }
        }
        
        if ($final) {
            $finalKey = sprintf('%d', Co::tid());
            $this->unset($finalKey);
        }
    }

}