<?php


namespace suframe\manage\components;


use suframe\core\traits\Singleton;
use Swoole\Client;
use swoole_client;

class HttpProxy
{
    use Singleton;

    public function __construct()
    {
        $this->getClient();
    }

    public function dispatch(\Swoole\Server $serv, $fd, $reactor_id, $data)
    {
        echo "dispatch \n";
        $data = explode("\r\n", $data);
        $info = [
            'method' => $data[0],
            'fd' => $fd,
        ];
        $this->getClient()->send(json_encode($info));
        $rs = $this->getClient()->recv();
        $this->response($serv, $fd, $rs);
        $serv->close($fd);
    }

    protected $client;

    protected function getClient(){
        if($this->client){
            return $this->client;
        }


        $client = new Client(SWOOLE_SOCK_TCP | SWOOLE_KEEP);
        $client->connect('127.0.0.1', 9502);
        return $this->client = $client;
    }

    function response($serv, $fd, $respData)
    {
        //响应行
        $response = array(
            'HTTP/1.1 200',
        );
        //响应头
        $headers = array(
            'Server' => 'SwooleServer',
            'Content-Type' => 'text/html;charset=utf8',
            'Content-Length' => strlen($respData),
        );
        foreach ($headers as $key => $val) {
            $response[] = $key . ':' . $val;
        }
        //空行
        $response[] = '';
        //响应体
        $response[] = $respData;
        $send_data = join("\r\n", $response);
        $serv->send($fd, $send_data);
    }

}