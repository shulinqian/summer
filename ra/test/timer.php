<?php

use Swoole\Timer;

Timer::tick(1000, function(){
    echo "timeout\n";
});