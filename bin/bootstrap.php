<?php
/** @var Composer\Autoload\ClassLoader $loader */
$loader = require_once dirname(__DIR__) . '/vendor/autoload.php';
$loader->addPsr4("suframe\\", __DIR__ . '/../suframe/');

//还没加载
