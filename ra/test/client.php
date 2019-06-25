<?php

$arg = $argv[1] ?? 1;

$client = new swoole_client(SWOOLE_SOCK_TCP);
if (!$client->connect('127.0.0.1', 9500, -1))
{
    exit("connect failed. Error: {$client->errCode}\n");
}

switch ($arg){
    case 1:
        $data = [
            'api' => 'server/register',
            'args' => [
                'path' => '/news',
                'ip' => '127.0.0.1',
                'port' => rand(9000, 9010),
            ]
        ];
        break;
    case 2:
        $data = [
            'api' => 'server/get'
        ];
        break;
}

$client->send(json_encode($data));
echo $client->recv(), "\n";
$client->close();