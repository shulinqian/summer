<?php
return [
	'server' => [
		'listen' => '0.0.0.0',
		'port' => 9501,
	],
    'register' => [
        'listen' => '0.0.0.0',
        'port' => 9500,
    ],
    'dispatch_type' => 'http',
    'swoole' => [
        'worker_num' => 5,
        'max_request' => 1000000,
    ]
];