<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/9
 * Time: 11:05
 */

namespace One\Swoole;


use One\Facades\Log;

class WsController
{
    protected $frame;

    public function __construct(\swoole_websocket_frame $frame)
    {
        $this->frame = $frame;
    }

    public function __destruct()
    {
        Log::flushTraceId();
    }

    /**
     * @return Session
     */
    final protected function session()
    {
        return $this->request->session();
    }

    /**
     * @return Protocol
     */
    final protected function server()
    {
        return Protocol::getServer();
    }

}