<?php

namespace One\Facades;

/**
 * @package One\Facades
 * @mixin \One\Response
 * @method string error($msg, $code = 400) static
 * @method string json($data, $callback = null) static
 * @method string redirectMethod($m, $args = []) static
 * @method string redirect($url, $args = []) static
 * @method string getUrl($str, $data = []) static
 * @method string tpl($template, $data = []) static
 *
 */

class Response extends Facade
{
    protected static function getFacadeAccessor()
    {
        if (config('app.run_mode') == 'swoole') {
            return \One\Swoole\Response::class;
        }else{
            return \One\Response::class;
        }
    }
}