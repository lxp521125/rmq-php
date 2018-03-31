<?php
/**
 * <?php
/**
 * 
http://tmall.aitboy.cn/light.php
{"header":{"messageId":"29732be4-f256-4983-8c2b-f3318e82b4fe","name":"DiscoveryDevices","namespace":"AliGenie.Iot.Device.Discovery","payLoadVersion":1},"payload":{"accessToken":"20180310111542"}}
 */

require_once "./vendor/autoload.php";
require_once "./light/BotController.php";

function jsonExit($data){
    echo json_encode($data);
    die;
}
$model = new light\BotController();

$postdata = file_get_contents("php://input");
file_put_contents('a.log', $postdata);
jsonExit($model->handleMessage($postdata));