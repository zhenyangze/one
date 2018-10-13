<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 2018/10/12
 * Time: 下午9:37
 */

namespace App\Protocol;


use One\Facades\Log;
use One\Swoole\GlobalData;
use One\Swoole\Server;

class GlobalDataServer extends Server
{
    /**
     * @var GlobalData
     */
    private $global = null;

    public function __construct(\swoole_server $server, array $conf)
    {
        parent::__construct($server, $conf);
        $this->global = new GlobalData();
    }

    public function onReceive(\swoole_server $server, $fd, $reactor_id, $data)
    {
        $ar = json_decode($data, true);
        if (method_exists($this->global, $ar['m'])) {
            $ret = $this->global->{$ar['m']}(...$ar['args']);
            if (strpos($ar['m'], 'get') !== false) {
                $this->server->send($fd, $this->protocol->pack(json_encode($ret)));
            }
        } else {
            Log::warn("method {$ar['m']} not exist");
        }
    }

    public function onWorkerStart(\swoole_server $server, $worker_id)
    {

    }

    public function onClose(\swoole_server $server, $fd, $reactor_id)
    {

    }
}