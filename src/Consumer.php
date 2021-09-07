<?php
namespace App;
use App\Channel as AppChannel;
use PhpAmqpLib\Message\AMQPMessage;

class Consumer
{
       public function __construct(string $queueName){
              $this->queueName = $queueName;
       }

       public function run() : void{
              $appChannel = new AppChannel($this->queueName);
              $appChannel->declareConsumer(function(AMQPMessage $msg){
                     $body = json_decode($msg->getBody());
                     var_dump($body);
              })->runConsumer()->close();
       }
}
