<?php


namespace suframe\proxy\api;

use suframe\core\components\register\Client as ClientAlias;

class Server extends Base
{

    /**
     * @param $args
     * @return bool
     * @throws \Exception
     */
    public function register($args){
        $config = ClientAlias::getInstance()->reloadServer();
        $path = $args['path'];
        $server = $config->get('servers');
        //唯一key防止重复
        $key = md5($args['ip'] . $args['port']);
        if(isset($server[$path])){
            //已存在
            if($server[$path]->get($key)){
                throw new \Exception('exist');
            }
            $server[$path][$key] = ['ip' => $args['ip'], 'port' => $args['port']];
        } else {
            $server[$path] = [
                $key => ['ip' => $args['ip'], 'port' => $args['port']]
            ];
        }

        try{
            //写入配置
            ClientAlias::getInstance()->updateLocalFile($server);
            //通知服务更新
            \suframe\core\components\register\Server::getInstance()->notify();
            return true;
        } catch (\Exception $e){
            return false;
        }
    }

    /**
     * 获取全部服务
     * @return array
     */
    public function get(){
        $config = ClientAlias::getInstance()->reloadServer();
        $servers = $config->get('servers');
        return $servers->toArray();
    }

}