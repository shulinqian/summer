<?php

namespace suframe\core\components\proxy;

use suframe\core\components\Config;
use Exception;
use suframe\core\traits\Singleton;
use Zend\Http\Request;

/**
 * User: qian
 * Date: 2019/7/2 10:59
 */
class Client
{
    use Singleton;
    protected $registerServer;

    /**
     * @throws Exception
     */
    public function getRegisterServer()
    {
        if (!$this->registerServer) {
            $config = Config::getInstance()->get('registerServer');
            if (!$config) {
                throw new Exception('register server no config');
            }
            $client = new \Zend\Http\Client("http://{$config['ip']}:{$config['port']}");
            $client->setMethod(Request::METHOD_POST);
            $client->setOptions([
                'timeout' => 2,
            ]);
            $this->registerServer = $client;
        }
        return $this->registerServer;
    }

    /**
     * 注册
     * @throws Exception
     */
    public function register()
    {
        $app = Config::getInstance()->get('app');
        if (!$app) {
            throw new Exception('app no config');
        }
        $client = $this->getRegisterServer();

        $client->setParameterGet([
            'api' => 'server/register'
        ]);
        $client->setParameterPost($app->toArray());
        try {
            $response = $client->send();
        } catch (Exception $e) {
            throw new Exception('register server invalid');
        }

        if (!$response->isSuccess()) {
            throw new Exception('register server fail');
        }
    }

    /**
     * 更新
     */
    public function update()
    {

    }
}