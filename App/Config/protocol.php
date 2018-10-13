<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 2018/8/24
 * Time: 下午5:23
 * 各个协议配置文件
 */

return [
    'server' => [
        'server_type' => \One\Swoole\OneServer::SWOOLE_HTTP_SERVER,
        'port' => 8080,
        'action' => \App\Protocol\AppWebSocket::class,
        'mode' => SWOOLE_PROCESS,
        'sock_type' => SWOOLE_SOCK_TCP,
        'ip' => '0.0.0.0',
        'set' => [],
        'global_data' => [
            'ip' => '127.0.0.1',
            'port' => 8086,
            'protocol' => \One\Protocol\Frame::class
        ]
    ],
    'add_listener' => [
        [
            'port' => 8081,
            'action' => \App\Protocol\AppHttpServer::class,
            'mode' => SWOOLE_PROCESS,
            'ip' => '0.0.0.0',
            'set' => []
        ]
    ],
    'global_data_server' => [
        'ip' => '127.0.0.1',
        'action' => \App\Protocol\GlobalDataServer::class,
        'port' => 8086,
        'mode' => SWOOLE_BASE,
        'set' => [
            'worker_num' => 1,
            'reactor_num' => 1,
            'daemonize' => 1
        ],
        'protocol' => \One\Protocol\Frame::class
    ]
];

