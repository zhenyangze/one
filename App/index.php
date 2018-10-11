<?php

define('_APP_PATH_', __DIR__);

define('_APP_PATH_VIEW_', __DIR__ . '/View');

require __DIR__ . '/../One/run.php';

require __DIR__ . '/../vendor/autoload.php';

require _APP_PATH_ . '/config.php';

\One\Http\Router::loadRouter();

$req = new \One\Http\Request();
$res = new \One\Http\Response($req);

try {
    $router = new \One\Http\Router();
    list($req->class, $req->method, $mids, $action, $req->args) = $router->explain($req->method(), $req->uri(), $req, $res);
    $f = $router->getExecAction($mids, $action, $res);
    echo $f();
} catch (\One\Exceptions\HttpException $e) {
    echo (new \App\Exceptions\Handler())->render($e);
} catch (\One\Http\RouterException $e) {
    echo \App\Exceptions\Handler::render(new \One\Exceptions\HttpException($res, $e->getMessage(), $e->getCode()));
} catch (Exception $e) {
    echo $e->getMessage();
    \One\Facades\Log::debug($e);
}
