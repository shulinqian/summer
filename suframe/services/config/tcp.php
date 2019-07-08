<?php
return [
	'server' => [
		'listen' => '0.0.0.0',
		'port' => 8080,
	],
    'swoole' => [
        'log_file' => SUMMER_APP_ROOT . 'runtime/service.log',
        'pid_file' => SUMMER_APP_ROOT . 'runtime/service.pid'
    ],
];