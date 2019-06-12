<?php
/**
 * User: qian
 * Date: 2019/6/5 11:42
 */
namespace suframe\core\components\net\tcp;

use suframe\core\traits\Singleton;

/**
 * tcp 代理
 * Class HttpProxy
 * @package suframe\manage\components
 */
class Proxy {
	protected $counter = 0;
	protected $resultError = 0;
	protected $pool;

	public function __construct() {
	    $this->pool = new Pool('127.0.0.1', 9502);
	}

	public function dispatch(\Swoole\Server $server, $fd, $reactor_id, $info) {
		$client = $this->pool->get();
		if ($client) {
			//链接端口可能会出警告
			$ret = @$client->send($info);
            if($ret){
				//无法判断tcp 因为应用层无法获得底层TCP连接的状态，执行send或recv时应用层与内核发生交互，才能得到真实的连接可用状态
				$rs = $client->recv();
				if ($rs) {
                    $this->pool->put($client);
                    $server->send($fd, $rs);
                    $server->close($fd);
					return $rs;
				}
            }
			$client->close();
		}
        $server->close($fd);
        return null;
	}

}