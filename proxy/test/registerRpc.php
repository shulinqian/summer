#!/usr/bin/env php
<?php
/** @var Composer\Autoload\ClassLoader $loader */
use Composer\Autoload\ClassLoader;
use suframe\core\components\Config;
use Symfony\Component\Finder\Finder;

$loader = require_once dirname(__DIR__) . '/../vendor/autoload.php';
$loader->addPsr4("suframe\\", __DIR__ . '/../../suframe/');
$loader->addPsr4("app\\", __DIR__ . '/../../proxy/');
defined('SUMMER_APP_ROOT') or define('SUMMER_APP_ROOT', __DIR__ . '/../');
$args = $argv;
array_shift($args);
$summer = \suframe\services\Core::getInstance()->init();

$server = \suframe\core\components\register\Server::getInstance();
$rs = $server->buildRpcMeta();
file_put_contents(__DIR__ . '/.phpstorm.meta.php', $rs);
