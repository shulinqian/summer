<?php
namespace suframe\server\components;

use Zend\Http\Response;

class Proxy
{

    /**
     * 服务代理转发
     * @param \Swoole\Server $server
     * @param $fd
     * @param $reactor_id
     * @param $data
     * @return mixed
     */
    public function dispatch(\Swoole\Server $server, $fd, $reactor_id, $data){
        $data = json_decode($data, true);
        $apiName = explode('/', $data['api']);
        $methodName = array_pop($apiName);
        $className = array_pop($apiName);
        $className = ucfirst($className);
        $apiName[] = $className;
        $apiName = implode('\\', $apiName);
        $apiClass = '\suframe\ra\api\\' . $apiName;

        if(!class_exists($apiClass)){
            $server->send($fd, json_encode(['code' => 404, 'msg' => 'api class not found']));
            $server->close($fd);
            return null;
        }
        $api = new $apiClass;
        if(!method_exists($api, $methodName)){
            $server->send($fd, json_encode(['code' => 404, 'msg' => 'api method not found']));
            $server->close($fd);
            return null;
        }
        $rs = $api->$methodName($data['args']);
        $response  = new Response();
        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->setContent($rs);
        $server->send($fd, $response->toString());
        $server->close($fd);
        return $rs;
    }

}
