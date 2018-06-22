<?php

namespace One\Swoole;


class Response extends \One\Response
{

    public function header($key, $val, $replace = true, $code = null)
    {
        Swoole::$response->header($key, $val, $replace);
        if ($code) {
            $this->code($code);
        }
    }

    public function code($code)
    {
        Swoole::$response->status($code);
    }

    public function cookie()
    {
        Swoole::$response->cookie(...func_get_args());
    }
}