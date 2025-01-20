<?php

namespace App\Contexts\Tweet\Infrastructure\Events\Publishers;

use App\Contexts\Tweet\Domain\Events\Publishers\TweetEventPublisherInterface;
use App\Contexts\Tweet\Domain\Events\DomainEvent;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQTweetPublisher implements TweetEventPublisherInterface
{
    private const EXCHANGE_NAME = 'tweets';
    private const QUEUE_NAME = 'tweet_events';
    private AMQPStreamConnection $connection;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            config('queue.connections.rabbitmq.host'),
            config('queue.connections.rabbitmq.port'),
            config('queue.connections.rabbitmq.user'),
            config('queue.connections.rabbitmq.password')
        );
    }

    public function publish(array $events): void
    {
        $channel = $this->connection->channel();

        // Declaramos el exchange y la cola
        $channel->exchange_declare(self::EXCHANGE_NAME, 'direct', false, true, false);
        $channel->queue_declare(self::QUEUE_NAME, false, true, false, false);
        $channel->queue_bind(self::QUEUE_NAME, self::EXCHANGE_NAME);

        foreach ($events as $event) {
            if (!$event instanceof DomainEvent) {
                continue;
            }

            // Convertimos el evento a JSON
            $messageBody = json_encode($event->toArray());

            $message = new AMQPMessage(
                $messageBody,
                [
                    'content_type' => 'application/json',
                    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
                ]
            );

            $channel->basic_publish($message, self::EXCHANGE_NAME);
        }

        $channel->close();
        $this->connection->close();
    }
}