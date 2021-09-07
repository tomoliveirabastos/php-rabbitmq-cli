<?php

namespace App;

use App\Channel as AppChannel;

class Sender
{
       public function __construct(string $queueName){
              $this->queueName = $queueName;
       }

       public function send(array $parameters): void
       {
              $appChannel = new AppChannel($this->queueName);
              $appChannel->declareSender($parameters);
       }
}
