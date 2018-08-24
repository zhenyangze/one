<?php
/**
 * swoole 运行这个文件
 * php swoole.php
 */
define('_APP_PATH_',__DIR__);

define('_APP_PATH_VIEW_',__DIR__.'/View');

require __DIR__.'/../One/run.php';

require __DIR__.'/../vendor/autoload.php';

require _APP_PATH_.'/config.php';

\One\Http\Router::loadRouter();

require _APP_PATH_.'/protocol.php';

\One\Swoole\Protocol::runAll();



