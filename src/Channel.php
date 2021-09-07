<?php

namespace App;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Channel
{
       private string $host;
       private int $port;
       private string $user;
       private string $password;
       private string $vhost;
       private string $queueName;
       private AMQPStreamConnection $amqpChannel;
       public function __construct(string $queueName)
       {
              $this->queueName = $queueName;
              $this->host = $_ENV["RABBITMQ_HOST"];
              $this->port = $_ENV["RABBITMQ_PORT"];
              $this->user = $_ENV["RABBITMQ_USER"];
              $this->password = $_ENV["RABBITMQ_PASSWORD"];
              $this->vhost = $_ENV["RABBITMQ_VHOST"];
              $this->amqpChannel = new AMQPStreamConnection(
                     $this->host,
                     $this->port,
                     $this->user,
                     $this->password,
                     $this->vhost
              );
              $this->channel = $this->amqpChannel->channel();
       }

       public function declareSender(array $payload): void
       {
              $this->channel->queue_declare($this->queueName, false, false, false, false);
              $msg = new AMQPMessage(json_encode($payload), [
                     "content_type" => "application/json",
                     "delivery_mode" => AMQPMessage::DELIVERY_MODE_PERSISTENT
              ]);
              $this->channel->basic_publish($msg, "", $this->queueName);
              $this->close();
       }

       public function declareConsumer(callable $callback): self
       {
              $channel = $this->channel;
              $channel->queue_declare($this->queueName, false, false, false, false);
              $channel->basic_consume($this->queueName, "", false, true, false, false, $callback);
              return $this;
       }

       public function runConsumer(): self
       {
              while ($this->channel->is_open()) {
                     $this->channel->wait();
              }

              return $this;
       }

       public function close(): self
       {
              $this->channel->close();
              $this->amqpChannel->close();
              return $this;
       }
}
