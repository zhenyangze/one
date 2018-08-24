<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 2018/8/24
 * Time: 下午5:23
 */

use \One\Swoole\Protocol;

$obj = new Protocol();
$obj->port(8080)->action(\App\Protocol\TestHttpServer::class);

$obj = new Protocol(Protocol::SWOOLE_WEBSOCKET_SERVER);
$obj->port(8081)->action(\App\Protocol\TestWebSocket::class);
