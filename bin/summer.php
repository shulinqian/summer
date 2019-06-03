#!/usr/bin/env php
<?php
require_once __DIR__ . '/bootstrap.php';
defined('SUMMER_APP_ROOT') or define('SUMMER_APP_ROOT', realpath(getcwd()));

$args = $argv;
//trim first command
array_shift($args);
$ret = (new \suframe\manage\Core())->run($args);
if(!empty($ret)){
	echo $ret."\n";
}