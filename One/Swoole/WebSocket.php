<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 2018/8/24
 * Time: 上午11:17
 */

namespace One\Swoole;


class WebSocket extends HttpServer
{
    public function onMessage(\swoole_server $server, \swoole_websocket_frame $frame)
    {

    }

    public function onHandShake(\swoole_http_request $request, \swoole_http_response $response)
    {

    }

    public function onOpen(\swoole_websocket_server $server, \swoole_http_request $request)
    {

    }
}