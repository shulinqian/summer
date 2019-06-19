#!/usr/bin/env php
<?php
/** @var Composer\Autoload\ClassLoader $loader */
use Composer\Autoload\ClassLoader;
use suframe\core\components\Config;

$loader = require_once dirname(__DIR__) . '/../vendor/autoload.php';
$loader->addPsr4("suframe\\", __DIR__ . '/../../suframe/');
defined('SUMMER_APP_ROOT') or define('SUMMER_APP_ROOT', __DIR__ . '/../');

Config::getInstance()->loadFile(SUMMER_APP_ROOT . 'config/config.php');

$server = new \suframe\ra\api\Server();

$args = [
    'path' => '/news',
    'ip' => '127.0.0.1',
];

for ($i = 0; $i < 4; $i++){
    $args['port'] = rand(9000, 9010);
    $server->register($args);
}

$args['port'] = 9001;
$server->register($args);
$args['port'] = 9001;
$server->register($args);