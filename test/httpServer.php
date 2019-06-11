<?php
$http = new Swoole\Http\Server("127.0.0.1", 9502);
$http->on('request', function (\Swoole\Http\Request $request, $response) {
    echo "request:" . $request->getData(), "\n";
	$response->end(json_encode($request->get));
//	$response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});
$http->start();