<?php

$serv = new \Swoole\Server('0.0.0.0', 9502);

$serv->on('connect', function ($serv, $fd){
	echo "Client:Connect.\n";
});

$serv->on('receive', function ($serv, $fd, $reactor_id, $data) {
	echo "收到数据： {$data}\n";
	$serv->send($fd, 'Swoole: '.$data . rand(1, 9999));
//	$serv->close($fd);
});
$serv->on('close', function ($serv, $fd) {
	echo "Client: Close.\n";
});
$serv->start();