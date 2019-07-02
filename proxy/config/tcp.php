<?php
return [
	'server' => [
		'listen' => '0.0.0.0',
		'port' => 9501,
	],
	//https://wiki.swoole.com/wiki/page/274.html 配置详见
	'swoole' => [
		'worker_num' => 5,
		'max_request' => 1000000,
	]
];