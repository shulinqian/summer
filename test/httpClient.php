<?php

go(function (){
    $cli = new \Swoole\Coroutine\Http\Client('127.0.0.1', 9500);
    $cli->post('/post.php', array("a" => '1234', 'b' => '456'));
    echo $cli->body;
    $cli->close();
});