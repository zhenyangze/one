<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 2018/8/24
 * Time: 上午11:08
 */

namespace One\Swoole;


class Server
{
    protected $conf = [];

    /**
     * @var \swoole_websocket_server
     */
    protected $server = null;

    public function __construct(\swoole_server $server,array $conf)
    {
        $this->server = $server;
        $this->conf = $conf;
    }

    public function onStart(\swoole_server $server)
    {
    }

    public function onShutdown(\swoole_server $server)
    {
    }

    public function onWorkerStart(\swoole_server $server, $worker_id)
    {
    }

    public function onWorkerStop(\swoole_server $server, $worker_id)
    {
    }

    public function onWorkerExit(\swoole_server $server, $worker_id)
    {
    }

    public function onConnect(\swoole_server $server, $fd, $reactor_id)
    {
    }

    public function onReceive(\swoole_server $server, $fd, $reactor_id, $data)
    {
    }

    public function onPacket(\swoole_server $server, $data, array $client_info)
    {
    }

    public function onClose(\swoole_server $server, $fd, $reactor_id)
    {
    }

    public function onBufferFull(\swoole_server $server, $fd)
    {
    }

    public function onBufferEmpty(\swoole_server $server, $fd)
    {
    }

    public function onTask(\swoole_server $server, $task_id, $src_worker_id, $data)
    {
    }

    public function onFinish(\swoole_server $server, $task_id, $data)
    {
    }

    public function onPipeMessage(\swoole_server $server, $src_worker_id, $message)
    {
    }

    public function onWorkerError(\swoole_server $server, $worker_id, $worker_pid, $exit_code, $signal)
    {
    }

    public function onManagerStart(\swoole_server $server)
    {
    }

    public function onManagerStop(\swoole_server $server)
    {
    }
}