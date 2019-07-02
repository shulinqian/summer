<?php
/**
 * User: qian
 * Date: 2019/7/1 16:36
 */

namespace suframe\register\components;

use suframe\core\components\Config;
use suframe\core\traits\Singleton;
use Swoole\Client;
use Swoole\Timer;
use Zend\Config\Writer\PhpArray;

/**
 * 服务定时同步
 * Class SyncServers
 * @package suframe\register\components
 */
class SyncServers
{
    use Singleton;

    protected $timer = [];

    /**
     * 创建定时器
     * @return bool
     */
    public function createTimer()
    {
        if ($this->timer) {
            return false;
        }
        $timerMs = Config::getInstance()->get('serverConfig.timerMs', 1000 * 60);
        $timer = Timer::tick($timerMs, function () {
            SyncServers::getInstance()->check();
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
        $config = Config::getInstance();
        $servers = $config->get('servers');
        $hasChange = false;
        //检测
        foreach ($servers as $server) {
            /** @var Config $item */
            foreach ($server as $key => $item) {
                $client = new Client(SWOOLE_SOCK_TCP);
                if (!$client->connect($item['ip'], $item['port'], -1)) {
                    $hasChange = true;
                    //剔除
                    echo "{$item['ip']}:{$item['port']}: has error\n";
                    unset($server[$key]);
                    continue;
                }
                $client->close();
            }
        }
        if ($hasChange) {
            $servers = $config->get('servers');
            $file = SUMMER_APP_ROOT . 'config/servers.php';
            $writer = new PhpArray();
            $writer->toFile($file, $servers->toArray());
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
        go(function (){
            try {
                $config = Config::getInstance();
                $servers = $config->get('servers');
                //通知更新
                foreach ($servers as $server) {
                    /** @var Config $item */
                    foreach ($server as $key => $item) {
                        $cli = new \Swoole\Coroutine\Http\Client($item['ip'], $item['port']);
                        $cli->post('/summer', array("command" => 'UPDATE_SERVERS', "command2" => 'UPDATE_SERVERS'));
                        $cli->close();
                    }
                }
            } catch (\Exception $e) {
            }
        });
    }

}