<?php
return [
	'server' => [
		'listen' => '0.0.0.0',
		'port' => 9500,
	],
	//https://wiki.swoole.com/wiki/page/274.html 配置详见
	'swoole' => [
		'log_file' => SUMMER_APP_ROOT . 'runtime/ra.log',
		'pid_file' => SUMMER_APP_ROOT . 'runtime/ra.pid'
	],
    'dispatch_type' => 'tcp'
];