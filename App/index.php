<?php

define('_APP_PATH_',__DIR__);

define('_APP_PATH_VIEW_',__DIR__.'/View');

require __DIR__.'/../One/run.php';

require __DIR__.'/../vendor/autoload.php';

require _APP_PATH_.'/config.php';

\One\Facades\Router::loadRouter();

try{
    echo \One\Facades\Router::exec();
}catch (\One\Exceptions\HttpException $e){
    echo (new \App\Exceptions\Handler())->render($e);
}catch (Exception $e){
    echo $e->getMessage();
    \One\Facades\Log::debug($e);
}
