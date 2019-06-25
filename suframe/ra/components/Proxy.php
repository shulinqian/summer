<?php
namespace suframe\ra\components;

use Swoole\Http\Request;
use Zend\Http\Response;

class Proxy
{

    /**
     * 服务代理转发
     * @param Request $request
     * @return false|string
     */
    public function dispatch(Request $request){
        $data = $request->get;
        if(!isset($data['api'])){
            return $this->response404('api name missing');
        }

        $apiName = explode('/', $data['api']);
        $methodName = array_pop($apiName);
        $className = array_pop($apiName);
        $className = ucfirst($className);
        $apiName[] = $className;
        $apiName = implode('\\', $apiName);
        $apiClass = '\suframe\ra\api\\' . $apiName;

        if(!class_exists($apiClass)){
            return $this->response404('api class not found');
        }
        $api = new $apiClass;
        if(!method_exists($api, $methodName)){
            return $this->response404('api method not found');
        }
        try{
            $args = $data['args'] ?? [];
            $rs = $api->$methodName($args);
            return json_encode(['code' => 200, $rs]);
        } catch (\Exception $e){
            return json_encode(['code' => $e->getCode(), 'msg' => $e->getMessage()]);
        }
    }

    public function response404($message){
        return json_encode(['code' => 404, 'msg' => $message]);
    }

}
