<?php

use Inhere\Console\Console;

return [
	'console' => [
		'name' => 'summer manage',
		'description' => 'welcome to use summer framework',
		'debug' => Console::VERB_ERROR,
		'profile' => false,
		'version' => '0.0.1',
		'publishAt' => '2019.06.04',
		'updateAt' => '2019.06.04',
		'rootPath' => '',
		'strictMode' => false,
		'hideRootPath' => true,

		// 'timeZone' => 'Asia/Shanghai',
		// 'env' => 'prod', // dev test prod
		// 'charset' => 'UTF-8',

		'logoText' => file_get_contents(__DIR__ . '/logo'),
		'logoStyle' => \Inhere\Console\Component\Style\Style::SUCCESS,
	]
];