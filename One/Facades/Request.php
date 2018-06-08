<?php

namespace One\Facades;

/**
 * @package One\Facades
 * @mixin \One\Request
 * @method string ip() static
 * @method string server($name) static
 * @method string userAgent() static
 * @method string uri() static
 * @method string id() static
 * @method string get($key = null, $default = null) static
 * @method string post($key = null, $default = null) static
 * @method string arg($i = null, $default = null) static
 * @method string res($key = null, $default = null) static
 * @method string cookie($key, $default = null) static
 * @method string input() static
 * @method array json() static
 * @method array file() static
 * @method string method() static
 * @method bool isAjax() static
 */

class Request extends Facade
{
    protected static function getFacadeAccessor()
    {
        if (config('app.run_mode') == 'swoole') {
            return \One\Swoole\Request::class;
        } else {
            return \One\Request::class;
        }
    }
}