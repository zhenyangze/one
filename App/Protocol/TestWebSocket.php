<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 2018/8/24
 * Time: 下午4:59
 */

namespace App\Protocol;


use One\Swoole\Protocol;
use One\Swoole\WebSocket;

class TestWebSocket extends WebSocket
{
    public function onMessage(\swoole_server $server, \swoole_websocket_frame $frame)
    {
        $data = $frame->data;
        $arr = json_decode($data,true);
        Protocol::getServer()->bindName($frame->fd,$arr['name']);
//        Protocol::getServer()->pushByName($arr['name'],json_encode([$frame->fd,$arr['name']]));
    }


}