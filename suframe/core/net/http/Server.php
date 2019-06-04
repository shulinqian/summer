<?php
namespace suframe\core\net\http;

class Server{

    protected $server;

    public function create(){
        $http = new \Swoole\Http\Server("127.0.0.1", 9501);
        $http->on('request', function ($request, $response) {
            $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
        });
        return $this->server = $http;
    }

    public function start(){
        $this->getServer()->start();
    }

    /**
     * @return mixed
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