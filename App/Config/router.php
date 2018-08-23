<?php

use One\Http\Router;

Router::get('/',\App\Controllers\IndexController::class.'@index');
Router::get('/test',\App\Controllers\IndexController::class.'@test');
Router::get('/user/{id}',[
    'use' => \App\Controllers\IndexController::class.'@test',
    'as' => 'user'
]);

