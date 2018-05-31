<?php

use One\Facades\Router;

Router::shell('/',\App\Controllers\IndexController::class.'@index');
Router::get('/',\App\Controllers\IndexController::class.'@index');
Router::get('/user/{id}',[
    'use' => \App\Controllers\IndexController::class.'@test',
    'as' => 'user'
]);



Router::group();