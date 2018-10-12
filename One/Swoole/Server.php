<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 2018/8/24
 * Time: 上午11:08
 */

namespace One\Swoole;

use One\Protocol\ProtocolAbstract;

class Server
{
    protected $conf = [];

    /**
     * @var ProtocolAbstract
     */
    protected $protocol = null;

    /**
     * @var \swoole_websocket_server
     */
    protected $server = null;

    public function __construct(\swoole_server $server, array $conf)
    {
        $this->server = $server;
        $this->conf = $conf;
        if (isset($conf['protocol'])) {
            $this->protocol = new $conf['protocol'];
        }
    }

    public function onStart(\swoole_server $server)
    {
    }

    public function onShutdown(\swoole_server $server)
    {

    }

    public function onWorkerStart(\swoole_server $server, $worker_id)
    {
        OneServer::getServer()->worker_id = $worker_id;
        OneServer::getServer()->worker_pid = $server->worker_pid;
        OneServer::getServer()->is_task = $server->taskworker ? true : false;
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

    public final function __receive(\swoole_server $server, $fd, $reactor_id, $data)
    {
        if (isset($conf['protocol'])) {
            $this->protocol->decode($data, $fd, function ($d) use ($server, $reactor_id, $fd) {
                $this->onReceive($server, $fd, $reactor_id, $d);
            });
        } else {
            $this->onReceive($server, $fd, $reactor_id, $data);
        }
    }

    public function onReceive(\swoole_server $server, $fd, $reactor_id, $data)
    {

    }

    public function onPacket(\swoole_server $server, $data, array $client_info)
    {
    }

    public function onClose(\swoole_server $server, $fd, $reactor_id)
    {
        OneServer::getServer()->unBindFd($fd);
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