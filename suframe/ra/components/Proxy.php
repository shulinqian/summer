<?php
namespace suframe\ra\components;

use Zend\Http\Response;

class Proxy
{

    public function dispatch(\Swoole\Server $server, $fd, $reactor_id, $data){
        $data = json_decode($data, true);
        $data = [
            'api' => 'server/register',
            'args' => [
                'path' => '/news',
                'ip' => '127.0.0.1',
                'port' => 9005,
            ]
        ];
        $apiName = explode('/', $data['api']);
        $methodName = array_pop($apiName);
        $className = array_pop($apiName);
        $className = ucfirst($className);
        $apiName[] = $className;
        $apiName = implode('\\', $apiName);
        $apiClass = '\suframe\ra\api\\' . $apiName;
        if(!class_exists($apiName)){
            $server->send($fd, json_encode(['code' => 404, 'msg' => 'api class not found']));
            $server->close($fd);
        }
        $api = new $apiClass;
        if(method_exists($api, $methodName)){
            $server->send($fd, json_encode(['code' => 404, 'msg' => 'api method not found']));
            $server->close($fd);
        }
        $rs = $api->$methodName($data['args']);
        $rs = json_encode(['code' => 200, $rs]);
        $response  = new Response();
        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->getHeaders()->addHeaders([
            'HeaderField1' => 'header-field-value',
            'HeaderField2' => 'header-field-value2',
        ]);
        $response->setContent($rs);

        echo $response->toString(), "\n\n";
        $server->send($fd, $response->toString());
        $server->close($fd);
        return $rs;
    }

}
