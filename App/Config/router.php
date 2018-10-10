<?php

use One\Http\Router;
use App\Middle\TestMiddle;

Router::get('/', \App\Controllers\IndexController::class . '@index');
Router::get('/test/{id}/{id}',[
    'use' => \App\Controllers\IndexController::class . '@test',
    'middle' => [
        TestMiddle::class . '@a', TestMiddle::class . '@b', TestMiddle::class . '@c'
    ]
]);
