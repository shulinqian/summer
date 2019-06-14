<?php
$port = $argv[1] ?? 9502;
$http = new Swoole\Http\Server("127.0.0.1", $port);
$http->on('request', function (\Swoole\Http\Request $request, $response) use($port){
    echo "request {$port}:" . $request->getData(), "\n";
	$response->end(json_encode($request->get));
//	$response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});

$http->on('close', function ($serv, $fd) {
	echo "Client: Close.\n";
});
$http->start();