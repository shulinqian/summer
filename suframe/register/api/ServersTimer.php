<?php

namespace suframe\register\api;

use suframe\register\components\SyncServers;

/**
 * 定时器接口
 * Class ServersTimer
 * @package suframe\register\api
 */
class ServersTimer extends Base
{

    protected $checkTimer;

    /**
     * 启动
     * @return bool
     */
    public function start()
    {
        return SyncServers::getInstance()->createTimer();
    }

    /**
     * 检查
     * @return array
     */
    public function check()
    {
        return SyncServers::getInstance()->checkTimer();
    }

    /**
     * 清除定时器
     * @return bool
     */
    public function clear()
    {
        return SyncServers::getInstance()->clearTimer();
    }
}