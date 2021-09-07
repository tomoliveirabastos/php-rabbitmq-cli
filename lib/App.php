<?php

namespace Minicli;

use App\Consumer;
use App\Sender;

class App
{
       private function parseArgv(array $argv)
       {
              $arr = array_map(function (string $item) {
                     [$key, $value] = explode("=", $item);
                     return [
                            $key => $value
                     ];
              }, $argv);

              return array_reduce($arr, function ($a, $b) {
                     $keyName = key($b);
                     $a[$keyName] = $b[$keyName];
                     return $a;
              }, []);
       }

       public function runCommand(array $argv)
       {

              $onlyParameters = array_slice($argv, 1);

              $params = $this->parseArgv($onlyParameters);

              if (!array_key_exists("feature", $params)) {
                     throw new \Exception("Has no [feature] parameter");
              }

              if (!array_key_exists("queue", $params)) {
                     throw new \Exception("Has no [queue] parameter");
              }

              $execute = [
                     "consumer" => function (array $allParameters) {

                            $consumer = new Consumer($allParameters["queue"]);

                            if(array_key_exists('commands', $allParameters)){
                                   $commands = array_map(function($command){
                                          return trim($command);
                                   }, explode(",", $allParameters["commands"]));

                                   $consumer->run($commands);
                                   return;
                            }

                            $consumer->run();
                     },
                     "sender" => function (array $allParameters) {
                            $sender = new Sender($allParameters["queue"]);

                            unset($allParameters["queue"]);
                            unset($allParameters["feature"]);

                            $sender->send($allParameters);
                     }
              ];

              $execute[$params["feature"]]($params);
       }
}
