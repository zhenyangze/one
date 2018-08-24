<?php

define('_APP_PATH_',__DIR__);

define('_APP_PATH_VIEW_',__DIR__.'/View');

require __DIR__.'/../One/run.php';

require __DIR__.'/../vendor/autoload.php';

require _APP_PATH_.'/config.php';

\One\Http\Router::loadRouter();

try{
    $req = new \One\Http\Request();
    $res = new \One\Http\Response($req);
    echo (new \One\Http\Router())->exec($req,$res);
}catch (\One\Exceptions\HttpException $e){
    echo (new \App\Exceptions\Handler())->render($e);
}catch (Exception $e){
    echo $e->getMessage();
    \One\Facades\Log::debug($e);
}
