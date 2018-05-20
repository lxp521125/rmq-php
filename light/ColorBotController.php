<?php

namespace light;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Bluerhinos\phpMQTT;
use publictool\sendMessage;

class ColorBotController
{

    public function handleMessage($postdata)
    {
        $data = json_decode($postdata);
        if(empty($data)){
            return [];
        }
        switch ($data->header->name) {
            case 'DiscoveryDevices':
                if ($data->header->namespace == 'AliGenie.Iot.Device.Discovery') {
                    return $this->maclist($data->header->messageId);
                }
                break;

            case 'TurnOn':
                if ($data->header->namespace == 'AliGenie.Iot.Device.Control') {
                    return $this->onlight($data->header->messageId, $data->payload->deviceId);
                }
                break;
            case 'TurnOff':
                if ($data->header->namespace == 'AliGenie.Iot.Device.Control') {
                    return $this->offlight($data->header->messageId, $data->payload->deviceId);
                }
                break;
            case 'SetBrightness':
                if ($data->header->namespace == 'AliGenie.Iot.Device.Control') {
                    return $this->SetBrightness($data->header->messageId, $data->payload->deviceId, $data->payload->value);
                }
                break;
            case 'AdjustUpBrightness':
                if ($data->header->namespace == 'AliGenie.Iot.Device.Control') {
                    return $this->AdjustUpBrightness($data->header->messageId, $data->payload->deviceId);
                }
                break;
            case 'AdjustDownBrightness':
                if ($data->header->namespace == 'AliGenie.Iot.Device.Control') {
                    return $this->AdjustDownBrightness($data->header->messageId, $data->payload->deviceId);
                }
                break;
            case 'SetMode':
                if ($data->header->namespace == 'AliGenie.Iot.Device.Control') {
                    return $this->SetMode($data->header->messageId, $data->payload->deviceId, $data->payload->value);
                }
                break;
            default:
                // code...
                break;
        }
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
                    'deviceId' => 'color-light001',
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
                    'actions' => ["TurnOn","TurnOff",'SetBrightness', 'AdjustUpBrightness', 'AdjustDownBrightness', 'SetMode'],
                    'extensions' => [
                        "ex1"=>""
                    ]
                ]

            ]
        ]
    ];
}

function onlight($messageId, $deviceId){
    sendMessage::mqSendCode('color-light', 'open');
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
    sendMessage::mqSendCode('color-light', 'close');

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
public function SetBrightness($messageId, $deviceId, $value = 0)
    {
        if($value == "max"){
            sendMessage::mqSendCode('color-light', 'open');
        }elseif($value == "min"){
            sendMessage::mqSendCode('color-light', 'close');
        }else{
            $value = intval($value);
            sendMessage::mqSendCode('color-light', 'set-'.$value);
        }
        return array(
            'header' => 
           array(
              'namespace' => 'AliGenie.Iot.Device.Control',
              'name' => 'SetBrightnessResponse',
              'messageId' => $messageId,
              'payLoadVersion' => 1,
           ),
            'payload' => 
           array(
              'deviceId' => $deviceId,
           ),
         );
    }
    
    public function AdjustUpBrightness($messageId, $deviceId)
    {
        sendMessage::mqSendCode('color-light', 'up');
        return array(
            'header' => 
           array(
              'namespace' => 'AliGenie.Iot.Device.Control',
              'name' => 'AdjustUpBrightnessResponse',
              'messageId' => $messageId,
              'payLoadVersion' => 1,
           ),
            'payload' => 
           array(
              'deviceId' => $deviceId,
           ),
         );
    }
    public function AdjustDownBrightness($messageId, $deviceId)
    {
        sendMessage::mqSendCode('color-light', 'down');
        
        return array(
            'header' => 
           array(
              'namespace' => 'AliGenie.Iot.Device.Control',
              'name' => 'AdjustDownBrightnessResponse',
              'messageId' => $messageId,
              'payLoadVersion' => 1,
           ),
            'payload' => 
           array(
              'deviceId' => $deviceId,
           ),
         );
    }
    public function SetMode($messageId, $deviceId, $value)
    {
        sendMessage::mqSendCode('color-light', 'mode-'.$value);
        
        return array(
            'header' => 
           array(
              'namespace' => 'AliGenie.Iot.Device.Control',
              'name' => 'SetModeResponse',
              'messageId' => $messageId,
              'payLoadVersion' => 1,
           ),
            'payload' => 
           array(
              'deviceId' => $deviceId,
           ),
         );
    }
}