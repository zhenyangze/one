<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 2018/8/24
 * Time: 下午4:59
 */

namespace App\Protocol;


use One\Http\Router;
use One\Http\RouterException;
use One\Swoole\WebSocket;

class TestWebSocket extends WebSocket
{
    public function onMessage(\swoole_server $server, \swoole_websocket_frame $frame)
    {
        $info = json_decode($frame->data, true);

        try {
            $router = new Router();
            list($frame->class, $frame->method, $mids, $action, $frame->args) = $router->explain('ws', $info['uri'], [], $frame);
            $f = $router->getExecAction($mids, $action, $frame, $server);
            $data = $f();
        } catch (RouterException $e) {
            $data = $e->getMessage();
        } catch (\Throwable $e) {
            $data = $e->getMessage();
        }

        if ($data) {
            $server->push($data, $frame->fd);
        }
    }
}