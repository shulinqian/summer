<?php


namespace suframe\ra\api;


use suframe\core\components\Config;
use Swoole\Client;
use Zend\Config\Writer\PhpArray;

class Server extends Base
{

    public function register($args){
        $config = Config::getInstance();
        $pathConfig = $config->get('servers.' . $args['path']);
        //唯一key防止重复
        $key = md5($args['ip'] . $args['port']);
        if($pathConfig){
            if($pathConfig->get($key)){
                return true;
            }
            $argsData = [
                $key => ['ip' => $args['ip'], 'port' => $args['port']]
            ];
            $registerConfig = new Config($argsData);
            $pathConfig->merge($registerConfig);
        } else {
            $argsData = [
                'servers' => [
                    $args['path'] => [
                        $key => ['ip' => $args['ip'], 'port' => $args['port']]
                    ]
                ]
            ];
            $registerConfig = new Config($argsData);
            $config->merge($registerConfig);
        }
        $writer = new PhpArray();
        $server = $config->get('servers');
        $file = SUMMER_APP_ROOT . 'config/servers.php';
        try{
            $writer->toFile($file, $server);
            return true;
        } catch (\Exception $e){
            return false;
        }
    }

    /**
     * 同步服务
     * @param $server
     */
    protected function syncServers($config){
        //获取服务列表，通知更新
        foreach ($config as $path => $servers){
            foreach ($servers as $server) {
                $client = new Client(SWOOLE_SOCK_TCP);
                if (!$client->connect($server['host'], $server['port'], -1))
                {
                    continue;
                }
                $client->send("UPDATE_SERVERS");
                $rs = $client->recv();
                if($rs = 'ok'){
                    //更新服务状态为已更新
                } else {
                    //更新服务状态为更新失败
                }
            }

        }
    }

}