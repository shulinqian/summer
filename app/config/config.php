<?php
$servers = [];
if(file_exists(__DIR__ . '/servers.php')){
    $servers = require __DIR__ . '/servers.php';
}

return [
    'registerServer' => [
        'ip' => '127.0.0.1',
        'port' => 9500
    ],
    'app' => require __DIR__ . '/app.php',
    'tcp' => require __DIR__ . '/tcp.php',
    'servers' => $servers,
];