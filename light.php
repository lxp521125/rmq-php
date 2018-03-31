<?php
/**
 * <?php
/**
 * 
http://tmall.aitboy.cn/light.php
{"header":{"messageId":"29732be4-f256-4983-8c2b-f3318e82b4fe","name":"DiscoveryDevices","namespace":"AliGenie.Iot.Device.Discovery","payLoadVersion":1},"payload":{"accessToken":"20180310111542"}}
 */

require_once "./vendor/autoload.php";
use light\BotController;

function jsonExit($data){
    echo json_encode($data);
    die;
}

while(1){
    if(file_get_contents('1.txt') == 1){
        `sudo echo 1 > /sys/class/gpio/gpio21/value`;
        file_get_contents('1.txt', '');
        sleep(1);
    }
    if(file_get_contents('1.txt') == 0){
        `sudo echo 0 > /sys/class/gpio/gpio21/value`;
        file_get_contents('1.txt', '');
        sleep(1);
    }
}