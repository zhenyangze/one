<?php

namespace One\Facades;

/**
 * Class Session
 * @package One\Facades
 * @method void set($key, $val) static
 * @method mixed get($key = null) static
 * @method void del($key) static
 */
class Session extends Facade
{
    protected static function getFacadeAccessor()
    {
        if (config('app.run_mode') == 'swoole') {
            return \One\Swoole\Session::class;
        }else{
            return \One\Session::class;
        }
    }
}