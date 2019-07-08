#!/usr/bin/env php
<?php
/** @var Composer\Autoload\ClassLoader $loader */
use Composer\Autoload\ClassLoader;

$loader = require_once dirname(__DIR__) . '/../vendor/autoload.php';
$loader->addPsr4("suframe\\", __DIR__ . '/../../suframe/');
$loader->addPsr4("app\\", __DIR__ . '/../user/');
defined('SUMMER_APP_ROOT') or define('SUMMER_APP_ROOT', __DIR__ . '/');
$args = $argv;
array_shift($args);
$summer = \suframe\services\Core::getInstance()->init();

//启动控制台
$summer->run();