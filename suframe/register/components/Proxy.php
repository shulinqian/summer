<?php

namespace suframe\register\components;

use Exception;
use Swoole\Http\Request;

class Proxy
{
    /**
     * 服务代理转发
     * @param Request $request
     * @return false|string
     * @throws Exception
     */
    public function dispatch(Request $request)
    {
        $path = $request->server['path_info'];
        $path = ltrim($path, '/');
        $apiName = explode('/', $path);
        if ($apiName[0] !== 'summer') {
            throw new Exception('path error');
        }
        array_shift($apiName);
        $methodName = array_pop($apiName);
        $className = array_pop($apiName);
        $className = ucfirst($className);
        $apiName[] = $className;
        $apiName = implode('\\', $apiName);
        $apiClass = '\suframe\register\api\\' . $apiName;

        if (!class_exists($apiClass)) {
            throw new Exception('api class not found');
        }
        $api = new $apiClass;
        if (!method_exists($api, $methodName)) {
            throw new Exception('api method not found');
        }

        $args = $request->post ?: [];
        $rs = $api->$methodName($args);
        return $rs;
    }

}
