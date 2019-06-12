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
	use Singleton;
	protected $counter = 0;
	protected $resultError = 0;

	public function __construct() {
		$this->getClient();
	}

	public function dispatch(\Swoole\Server $serv, $fd, $reactor_id, $info) {
		$client = $this->getClient()->get();
		if ($client) {
			//链接端口可能会出警告
			$ret = @$client->send($info);
            if($ret){
				//无法判断tcp 因为应用层无法获得底层TCP连接的状态，执行send或recv时应用层与内核发生交互，才能得到真实的连接可用状态
				$rs = $client->recv();
				if ($rs) {
					$this->getClient()->put($client);
					$serv->send($fd, $rs);
					$serv->close($fd);
					return $rs;
				}
            }
			$client->close();
		}
        $serv->close($fd);
        return null;
	}

	/**
	 * @return Pool
	 */
	protected function getClient() {
		return Pool::getInstance(5);
	}

}