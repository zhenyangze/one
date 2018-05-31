<?php

namespace One\Swoole;


class Response extends \One\Response
{

    public function header($key, $val, $replace = true, $code = null)
    {
        Swoole::$response->header($key, $val, $replace);
        if($code){
            Swoole::$response->status(302);
        }
    }
}