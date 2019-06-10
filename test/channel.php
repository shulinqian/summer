<?php
use Swoole\Coroutine as co;
$chan = new co\Channel(1);
go(function () use ($chan) {
	$chan->push(['rand' => rand(1000, 9999), 'index' => 1]);

});

go(function () use ($chan) {
	while(1) {
		$data = $chan->pop();
		var_dump($data);
		$chan->push($data);
	}
});
swoole_event::wait();