<?php

namespace One\Swoole;


use One\ConfigTrait;

class Swoole
{
    use ConfigTrait;

    public static $response;

    public static $request;


    public static function init($request, $response)
    {
        self::$request = $request;
        self::$response = $response;
        self::$conf['start'](self::$request, self::$response);
    }

}