<?php
namespace suframe\services\components;

use Exception;
use suframe\core\components\Config;
use suframe\core\components\rpc\RpcUnPack;

class Proxy
{
    /**
     * 服务代理转发
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public function dispatch($data){
        $pack = new RpcUnPack($data);
        $path = $pack->get('path');
        $path = ltrim($path, '/');
        $apiName = explode('/', $path);
        if ($apiName[0] === 'summer') {
            $nameSpace = '\suframe\services\api\\';
        } else {
            $nameSpace = Config::getInstance()->get('app.apiNameSpace');
        }

        $className = array_pop($apiName);
        $className = ucfirst($className);
        $apiName[] = $className;
        $apiName = implode('\\', $apiName);
        $apiClass = $nameSpace . $apiName;

        if (!class_exists($apiClass)) {
            throw new Exception('api class not found:' . $apiClass);
        }
        $api = new $apiClass;
        $methodName = 'run';
        if (!method_exists($api, $methodName)) {
            throw new Exception('api method not found');
        }
        $rs = $api->$methodName($pack->get());
        return $rs;
    }

}
