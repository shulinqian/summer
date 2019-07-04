<?php

namespace suframe\core\components\register;

use suframe\core\components\Config;
use Exception;
use suframe\core\traits\Singleton;
use Swoole\Http\Request;
use Zend\Config\Writer\PhpArray;


/**
 * User: qian
 * Date: 2019/7/2 10:59
 */
class Client
{
    use Singleton;
    protected $registerServer;
    const COMMAND_UPDATE_SERVERS = 'updateServers';

    /**
     * @param Request $request
     * @throws Exception
     */
    public function execCommand(Request $request)
    {
        $command = $request->post['command'];
        $method = 'command' . ucfirst($command);
        if(!method_exists($this, $method)){
            throw new Exception('command not found');
        }
        return $this->$method($request);
    }

    protected $registerConfig = [];
    /**
     * @throws Exception
     */
    protected function getRegisterConfig(){
        if(!$this->registerConfig){
            $this->registerConfig = Config::getInstance()->get('registerServer');
            if (!$this->registerConfig) {
                throw new Exception('register server no config');
            }
        }
        return $this->registerConfig;
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
        $config = $this->getRegisterConfig();
        $client = new \Swoole\Coroutine\Http\Client($config['ip'], $config['port']);
        $client->post('/summer/server/register', $app->toArray());
        $rs = $client->body;
        $client->close();
        var_dump($rs);
    }

    /**
     * 更新
     * @param Request $request
     * @throws Exception
     */
    public function commandUpdateServers(Request $request)
    {
        $config = $this->getRegisterConfig();
        $client = new \Swoole\Coroutine\Http\Client($config['ip'], $config['port']);
        $client->get('/summer/server/get');
        $rs = $client->body;
        $client->close();
        $rs = json_decode($rs, true);
        $code = $rs['code'] ?? null;
        if($code !== 200){
            throw new Exception('update fail');
        }
        $data = $rs['data'] ?? [];
        $this->updateLocalFile($data);
        return true;
    }

    /**
     * 更新本地文件
     * @param mixed $data
     */
    public function updateLocalFile($data){
        $file = SUMMER_APP_ROOT . 'config/servers.php';
        $writer = new PhpArray();
        $writer->toFile($file, is_object($data) ? $data->toArray() : $data);
        $config = Config::getInstance();
        $config['servers'] = $data;
    }

    /**
     * @return Config
     */
    public function reloadServer(){
        $file = SUMMER_APP_ROOT . 'config/servers.php';
        $config = Config::getInstance();
        unset($config['servers']);
        $config->loadFileByName($file, 'servers');
        return $config;
    }
}