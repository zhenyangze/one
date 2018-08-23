<?php

return [
    'debug_log' => true,
    'default' => [
        'dns' => 'mysql:host=127.0.0.1;dbname=lysy',
        'username' => 'root',
        'password' => '123456',
        'ops' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    ]
];
