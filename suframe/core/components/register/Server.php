<?php
/**
 * User: qian
 * Date: 2019/7/1 16:36
 */

namespace suframe\core\components\register;

use suframe\core\components\Config;
use suframe\core\components\rpc\RpcPack;
use suframe\core\traits\Singleton;
use Swoole\Client;
use Swoole\Timer;

/**
 * 服务定时同步
 * Class SyncServers
 * @package suframe\register\components
 */
class Server
{
    use Singleton;

    protected $timer = [];


    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return \suframe\core\components\register\Client::getInstance()->reloadServer();
    }

    /**
     * 创建定时器
     * @return bool
     */
    public function createTimer()
    {
        if ($this->timer) {
            return false;
        }
        $timerMs = $this->getConfig()->get('proxy.timerMs', 1000 * 60);
        $timer = Timer::tick($timerMs, function () {
            static::getInstance()->check();
        });
        $this->timer = [
            'created_time' => date('Y-m-d H:i:s'),
            'timer' => $timer
        ];
        return true;
    }

    /**
     * 检测定时器
     * @return array
     */
    public function checkTimer()
    {
        return $this->timer;
    }

    /**
     * 清除定时器
     * @return bool
     */
    public function clearTimer()
    {
        if ($this->timer) {
            Timer::clear($this->timer['timer']);
            $this->timer = [];
            return true;
        }
        return false;
    }

    /**
     * 检测服务
     * @return bool
     */
    public function check()
    {
        $servers = $this->getConfig()->get('servers');
        $hasChange = false;
//        echo "check servers \n";
//        var_dump($servers->toArray());
        //检测
        foreach ($servers as $server) {
            /** @var Config $item */
            foreach ($server as $key => $item) {
                $client = new Client(SWOOLE_SOCK_TCP);
//                echo "check: {$item['ip']}:{$item['port']}\n";
                if (!@$client->connect($item['ip'], $item['port'], -1)) {
                    $hasChange = true;
                    //剔除
                    echo "{$item['ip']}:{$item['port']}: has error\n";
                    unset($server[$key]);
                    continue;
                }
                echo "ok\n";
                $client->close();
            }
        }
        if ($hasChange) {
            $config = Config::getInstance();
            $servers = $config->get('servers');
            \suframe\core\components\register\Client::getInstance()->updateLocalFile($servers->toArray());
            echo "notify \n";
            $this->notify();
        }
        return true;
    }

    /**
     * 耿直服务更新
     * @return bool
     */
    public function notify()
    {
        try {
            $servers = $this->getConfig()->get('servers');
            //通知更新
            $pack = new RpcPack('/summer/client/notify');
            $pack->add('command', \suframe\core\components\register\Client::COMMAND_UPDATE_SERVERS);
            foreach ($servers as $server) {
                /** @var Config $item */
                foreach ($server as $key => $item) {
                    $client = new \Co\Client(SWOOLE_SOCK_TCP);
                    if (!$client->connect($item['ip'], $item['port'], 0.5)) {
                        exit("connect failed. Error: {$client->errCode}\n");
                    }
                    $client->send($pack->pack());
//                    echo $client->recv();
                    $client->close();
                }
            }
        } catch (\Exception $e) {
        }
    }

}