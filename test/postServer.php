<?php
$http = new Swoole\Http\Server("127.0.0.1", 9999);
$http->on('request', function (\Swoole\Http\Request $request, $response) {
    $response->end(json_encode($request->post));
});
$http->start();
