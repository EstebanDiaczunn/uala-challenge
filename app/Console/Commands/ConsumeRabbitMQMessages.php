<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use App\Contexts\Timeline\Domain\Events\Handlers\TweetCreatedEventHandler;
use PhpAmqpLib\Exception\AMQPTimeoutException;

class ConsumeRabbitMQMessages extends Command
{
    protected $signature = 'timeline:consume';
    protected $description = 'Consume timeline events from RabbitMQ';

    public function __construct(
        private TweetCreatedEventHandler $eventHandler
    ) {
        parent::__construct();
    }

    public function handle(): never
    {
        while (true) {  // Bucle infinito
            try {
                $this->setupAndConsumeMessages();
            } catch (\Exception $e) {
                $this->error("Conexión perdida: " . $e->getMessage());
                $this->info("Intentando reconectar en 5 segundos...");
                sleep(5);
            }
        }
    }

    private function setupAndConsumeMessages()
    {
        $connection = new AMQPStreamConnection(
            config('queue.connections.rabbitmq.host'),
            config('queue.connections.rabbitmq.port'),
            config('queue.connections.rabbitmq.user'),
            config('queue.connections.rabbitmq.password')
        );

        $channel = $connection->channel();

        // Configuración de la cola
        $channel->exchange_declare('tweets', 'direct', false, true, false);
        $channel->queue_declare('tweet_events', false, true, false, false);
        $channel->queue_bind('tweet_events', 'tweets');

        $this->info(" [*] Esperando mensajes. Para salir presionar CTRL+C\n");

        // Callback con mejor manejo de errores
        $callback = function ($msg) {
            try {
                $data = json_decode($msg->body, true);
                $this->info(" [x] Procesando tweet: " . substr($data['content'], 0, 30) . "...");

                $this->eventHandler->handle($data);

                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                $this->info(" [✓] Tweet procesado correctamente\n");
            } catch (\Exception $e) {
                $this->error(" [x] Error: " . $e->getMessage());
                $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
            }
        };

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume('tweet_events', '', false, false, false, false, $callback);

        // Mantener viva la conexión
        while (count($channel->callbacks)) {
            try {
                $channel->wait(null, false, 30);  // Timeout de 30 segundos
            } catch (AMQPTimeoutException $e) {
                // Simplemente continuamos el ciclo
                $this->info(".");  // Indicador de que seguimos vivos
                continue;
            }
        }
    }
}