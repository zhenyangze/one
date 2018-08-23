<?php

namespace One\Facades;

/**
 * @package One\Facades
 * @mixin \One\Http\Router
 * @method void loadRouter() static
 * @method string exec() static
 * @method void group(array $rule, \Closure $route) static
 * @method void controller(string $path, string $controller) static
 * @method void shell(string $path, string | array $action) static
 * @method void get(string $path, string | array $action) static
 * @method void post(string $path, string | array $action) static
 * @method void put(string $path, string | array $action) static
 * @method void delete(string $path, string | array $action) static
 * @method void patch(string $path, string | array $action) static
 * @method void head(string $path, string | array $action) static
 * @method void options(string $path, string | array $action) static
 *
 */

class Router extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \One\Http\Router::class;
    }

    protected static function initArgs()
    {
        $req = new \One\Http\Request();
        $res = new \One\Http\Response($req);
        return [$req,$res];
    }
}

