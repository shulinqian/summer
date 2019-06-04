#!/usr/bin/env php
<?php
require_once __DIR__ . '/bootstrap.php';
defined('SUMMER_APP_ROOT') or define('SUMMER_APP_ROOT', realpath(getcwd()) . '/app/');

$args = $argv;
array_shift($args);
$summer = \suframe\manage\Core::getInstance()->init();

//可以设置自定义Config类
//$summer->setConfig()

//可以设置自定义命令
/*
$summer->getConsole()->registerCommands();
$summer->getConsole()->registerGroups();
$summer->getConsole()->addCommand();
$summer->getConsole()->addGroup();
$summer->getConsole()->addControllers();
$summer->getConsole()->addController();

例如：
$summer->getConsole()->addCommand('test', function (Inhere\Console\IO\Input $in, \Inhere\Console\IO\Output $out) {
	$cmd = $in->getCommand();
	$out->info('hello, this is a test command: ' . $cmd);
}, 'this is message for the command');
*/

//启动控制台
$summer->run($args);
