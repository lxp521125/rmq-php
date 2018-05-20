<?php

namespace light;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Bluerhinos\phpMQTT;

class BotController
{

    public function handleMessage($postdata)
    {
        $data = json_decode($postdata);
        if(empty($data)){
            return [];
        }
        if($data->header->name == 'DiscoveryDevices' && $data->header->namespace == 'AliGenie.Iot.Device.Discovery'){
            return $this->maclist($data->header->messageId);
        }
        if($data->header->name == 'TurnOn' && $data->header->namespace == 'AliGenie.Iot.Device.Control'){
            return $this->onlight($data->header->messageId, $data->payload->deviceId);
        }
        if($data->header->name == 'TurnOff' && $data->header->namespace == 'AliGenie.Iot.Device.Control'){
            return $this->offlight($data->header->messageId, $data->payload->deviceId);
        }
    }

    private function sendCode($code)
    {
        $connection = new AMQPStreamConnection('mq.aitboy.cn', 32772);
        $channel = $connection->channel();
        $channel->queue_declare('pi', false, true, false, false);
        $msg = new AMQPMessage($code);
        $channel->basic_publish($msg, 'pi'); //路由地址
    }
    
function maclist($messageId){
    return [
        'header' => [
            'namespace' => 'AliGenie.Iot.Device.Discovery',
            'name' => 'DiscoveryDevicesResponse',
            'messageId' => $messageId,
            'payLoadVersion' => 1
        ],
        'payload' => [
            'devices' => [
                [
                    'deviceId' => 'color-light',
                    'deviceName' => '彩灯',
                    'deviceType' => 'light',
                    'zone' => '',
                    'brand' => '',
                    'model' => '',
                    'icon' => 'https://ihcv0.ibroadlink.com/ec4appsysinfo/category2/TV.png',
                    'properties' => [
                        [
                            "name"=> "powerstate",
                            "value"=> "off"
                        ]
                    ],
                    'actions' => ["TurnOn","TurnOff"],
                    'extensions' => [
                        "ex1"=>""
                    ]
                ]

            ]
        ]
    ];
}
    private function mqttSendCode($code)
    {

        $server = "mq.aitboy.cn";     // change if necessary
        $port = 1883;                     // change if necessary
        $username = "";                   // set your username
        $password = "";                   // set your password
        $client_id = "tmall"; // make sure this is unique for connecting to sever - you could use uniqid()

        $mqtt = new phpMQTT($server, $port, $client_id);

        if ($mqtt->connect(true, NULL, $username, $password)) {
            $mqtt->publish("chuang.9741790.$.command", $code, 0);
            $mqtt->close();
        } else {
            echo "Time out!\n";
        }
    }

function onlight($messageId, $deviceId){
    $this->sendCode('open');
    $this->mqttSendCode('ON');
    
    return [
        'header' => [
            "namespace"=>"AliGenie.Iot.Device.Control",
            "name"=>"TurnOnResponse",
            "messageId"=>$messageId,
            "payLoadVersion"=>1
        ],
        'payload' => [
            'deviceId' => $deviceId
        ]
    ];
}

function offlight($messageId, $deviceId, $isOff = false){
    $this->sendCode('close');
    $this->mqttSendCode('OFF');

    return [
        'header' => [
            "namespace"=>"AliGenie.Iot.Device.Control",
            "name"=>"TurnOffResponse",
            "messageId"=>$messageId,
            "payLoadVersion"=>1
        ],
        'payload' => [
            'deviceId' => $deviceId
        ]
    ];
}

}