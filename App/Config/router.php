<?php

use One\Facades\Router;

Router::shell('/',\App\Controllers\IndexController::class.'@index');
Router::get('/',\App\Controllers\IndexController::class.'@index');
Router::get('/test',\App\Controllers\IndexController::class.'@test');
