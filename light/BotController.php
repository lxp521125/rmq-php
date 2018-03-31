<?php

namespace light;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


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
                    'icon' => 'https://git.cn-hangzhou.oss-cdn.aliyun-inc.com/uploads/aicloud/aicloud-proxy-service/41baa00903a71c97e3533cf4e19a88bb/image.png',
                    'properties' => [],
                    'actions' => ["TurnOn","TurnOff"],
                    'extensions' => [
                    ]
                ]

            ]
        ]
    ];
}

function onlight($messageId, $deviceId){
    $this->sendCode('open');
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