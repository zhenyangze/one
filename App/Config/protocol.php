<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 2018/8/24
 * Time: 下午5:23
 * 各个协议配置文件
 */

use \One\Swoole\Protocol;

return [
    [
        'server_type' => Protocol::SWOOLE_HTTP_SERVER,
        'port' => 8080,
        'action' => \App\Protocol\TestHttpServer::class,
        'mode' => SWOOLE_PROCESS,
        'sock_type' => SWOOLE_SOCK_TCP,
        'ip' => '0.0.0.0',
        'set' => [],
        'global_data' => [
            'ip' => '127.0.0.1',
            'port' => 8080,
        ]
    ],
    [
        'server_type' => Protocol::SWOOLE_WEBSOCKET_SERVER,
        'port' => 8081,
        'action' => \App\Protocol\TestWebSocket::class,
        'mode' => SWOOLE_PROCESS,
        'sock_type' => SWOOLE_SOCK_TCP,
        'ip' => '0.0.0.0',
        'set' => []
    ],
    'run_global_data' => [
        'ip' => '127.0.0.1',
        'port' => 8080,
    ]
];

