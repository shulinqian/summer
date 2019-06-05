<?php
namespace suframe\core\net\http;

use suframe\core\event\EventManager;
use Swoole\Http\Request;
use Swoole\Http\Response;

class Server{

    protected $server;

	/**
	 * @return \Swoole\Http\Server
	 */
    public function create(){
        $http = new \Swoole\Http\Server("127.0.0.1", 9501);
        $http->on('request', function (Request $request, Response $response) {
        	EventManager::get()->trigger('http.request', null, $request);
        	$out = "<h1>Hello Swoole . #".rand(1000, 9999)."</h1>"; //业务处理

			EventManager::get()->trigger('http.response.before',null, ['out' => $out]);
            $response->end($out);
            EventManager::get()->trigger('http.response.after',null, [
            	'request' => $request,
				'response' => $response,
				'out' => $out,
			]);
        });
        return $this->server = $http;
    }

	/**
	 * @throws \Exception
	 */
    public function start(){
        $this->getServer()->start();
    }

	/**
	 * @return mixed
	 * @throws \Exception
	 */
    public function getServer()
    {
        if(!$this->server){
            throw new \Exception('please create server');
        }
        return $this->server;
    }

    /**
     * @param mixed $server
     */
    public function setServer($server)
    {
        $this->server = $server;
    }

}