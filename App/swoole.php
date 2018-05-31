<?php
/**
 * swoole 运行这个文件
 * php swoole.php
 */
define('_APP_PATH_',__DIR__);
define('_APP_PATH_VIEW_',__DIR__.'/View');
require_once __DIR__.'/../One/run.php';
require_once __DIR__.'/../vendor/autoload.php';
require_once _APP_PATH_.'/config.php';
\One\Facades\Router::loadRouter();

\One\Swoole\Swoole::setConfig(['start' => function($request,$response){
    try{
        $response->write(\One\Facades\Router::exec());
    }catch (Exception $e){
        $response->write((new \App\Exceptions\Handler())->render($e));
    }
    \One\Facades\Facade::clear(\One\Swoole\Request::class);
    \One\Facades\Facade::clear(\One\Swoole\Response::class);
    \One\Facades\Facade::clear(\One\Swoole\Session::class);
    $response->end();
}]);


$http = new swoole_http_server("127.0.0.1", 9501);

$http->set([
    'daemonize' => 1,
    'log_file' => config('log.path').'/swoole.log'
]);

$http->on("start", function ($server) {
    echo "Swoole http server is started at http://127.0.0.1:9501\n";
});

$http->on("request", function ($request, $response) {
    \One\Swoole\Swoole::init($request, $response);
});

$http->start();

