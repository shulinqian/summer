<?php
return [
    'server' => [
        'listen' => '0.0.0.0',
        'port' => 9501,
    ],
    'swoole' => [
        'worker_num' => 1, //一般为cpu核数的1-4倍
        'max_request' => 1000000,
        'log_file' => __DIR__ . '/../runtime/swoole.log',
        'pid_file' => __DIR__ . '/../runtime/swoole.pid'
    ]
];