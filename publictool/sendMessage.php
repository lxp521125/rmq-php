<?php
namespace publictool;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Bluerhinos\phpMQTT;

class sendMessage
{
        
    public static function mqSendCode($pul = "pi", $code)
    {
        $connection = new AMQPStreamConnection('mq.aitboy.cn');
        $channel = $connection->channel();
        $channel->queue_declare($pul, false, true, false, false);
        $msg = new AMQPMessage($code);
        $channel->basic_publish($msg, $pul); //路由地址
    }

    public static function mqttSendCode($pul = "chuang.9741790.$.command", $code)
    {

        $server = "mq.aitboy.cn";     // change if necessary
        $port = 1883;                     // change if necessary
        $username = "";                   // set your username
        $password = "";                   // set your password
        $client_id = ""; // make sure this is unique for connecting to sever - you could use uniqid()

        $mqtt = new phpMQTT($server, $port, $client_id);

        if ($mqtt->connect(true, NULL, $username, $password)) {
            $mqtt->publish($pul, $code, 0);
            $mqtt->close();
        } else {
            echo "Time out!\n";
        }
    }
}
