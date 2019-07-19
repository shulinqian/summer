<?php
if(file_exists(__DIR__ . '/servers.php')){
    $servers = require __DIR__ . '/servers.php';
} else {
    $servers = [];
}
return [
    'app' => require __DIR__ . '/app.php',
    'tcp' => require __DIR__ . '/tcp.php',
    'servers' => $servers,
];