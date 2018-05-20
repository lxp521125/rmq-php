<?php

namespace chuanglian;

use publictool\sendMessage;

class BotController
{
    public function handleMessage($postdata)
    {
        $data = json_decode($postdata);
        if (empty($data)) {
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

    public function maclist($messageId)
    {
        return [
        'header' => [
            'namespace' => 'AliGenie.Iot.Device.Discovery',
            'name' => 'DiscoveryDevicesResponse',
            'messageId' => $messageId,
            'payLoadVersion' => 1,
        ],
        'payload' => [
            'devices' => [
                [
                    'deviceId' => 'chuanglian',
                    'deviceName' => '窗帘',
                    'deviceType' => 'curtain',
                    'zone' => '',
                    'brand' => '',
                    'model' => '',
                    'icon' => 'https://ihcv0.ibroadlink.com/ec4appsysinfo/category2/STB.png',
                    'properties' => [
                        [
                            "name"=>"powerstate",
                            "value"=> "off"
                        ]
                    ],
                    'actions' => ['TurnOn', 'TurnOff', 'SetBrightness', 'AdjustUpBrightness', 'AdjustDownBrightness', 'SetMode'],
                    'extensions' => [
                        'ext1'=>""
                    ],
                ],
            ],
        ],
    ];
    }

    public function onlight($messageId, $deviceId)
    {
        sendMessage::mqSendCode('chuang', 'open');

        return [
        'header' => [
            'namespace' => 'AliGenie.Iot.Device.Control',
            'name' => 'TurnOnResponse',
            'messageId' => $messageId,
            'payLoadVersion' => 1,
        ],
        'payload' => [
            'deviceId' => $deviceId,
        ],
    ];
    }

    public function offlight($messageId, $deviceId, $isOff = false)
    {
        sendMessage::mqSendCode('chuang', 'close');

        return [
        'header' => [
            'namespace' => 'AliGenie.Iot.Device.Control',
            'name' => 'TurnOffResponse',
            'messageId' => $messageId,
            'payLoadVersion' => 1,
        ],
        'payload' => [
            'deviceId' => $deviceId,
        ],
    ];
    }
    public function SetBrightness($messageId, $deviceId, $value = 0)
    {
        if($value == "max"){
            sendMessage::mqSendCode('chuang', 'open');
        }elseif($value == "min"){
            sendMessage::mqSendCode('chuang', 'close');
        }else{
            $value = intval($value);
            sendMessage::mqSendCode('chuang', 'set-'.$value);
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
        sendMessage::mqSendCode('chuang', 'up');
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
        sendMessage::mqSendCode('chuang', 'down');
        
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
        sendMessage::mqSendCode('chuang', 'mode-'.$value);
        
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
