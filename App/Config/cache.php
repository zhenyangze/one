<?php
return [
    'drive' => 'File', // File | Redis

    'file' => [
        'path' => _APP_PATH_ . '/RunCache/cache',
        'prefix' => 'one_'
    ],

    'redis' => [
        'host' => '127.0.0.1',
        'port' => 6379,
        'prefix' => 'one_'
    ]
];

