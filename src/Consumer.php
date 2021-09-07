<?php

namespace App;

use App\Channel as AppChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class Consumer
{
       public function __construct(string $queueName)
       {
              $this->queueName = $queueName;
       }

       public function run(?array $commands = []): void
       {

              $appChannel = new AppChannel($this->queueName);

              $appChannel->declareConsumer(function (AMQPMessage $msg) use($commands){
                     $body = json_decode($msg->getBody());

                     $phpBinaryFinder = new PhpExecutableFinder();

                     $params = ['sudo', $phpBinaryFinder->find(), $_ENV["PATH_TO_EXEC_SYSTEM"]];

                     foreach($commands as $command){
                            array_push($params, $command);
                     }
                     
                     $process = new Process($params);

                     $process->run();

                     var_dump($process->getOutput(), $phpBinaryFinder->find(), $body);
              })->runConsumer()->close();
       }
}
