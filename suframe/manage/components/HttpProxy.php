<?php

namespace suframe\manage\components;

use suframe\core\net\http\Response;
use suframe\core\traits\Singleton;

/**
 * http 代理
 * Class HttpProxy
 * @package suframe\manage\components
 */
class HttpProxy {
	use Singleton;
	protected $atomic;

	public function __construct() {
		$this->getClient();
		$this->atomic = new Atomic();
	}

	public function dispatch(\Swoole\Server $serv, $fd, $reactor_id, $data) {
		echo "dispatch \n";
		$data = explode("\r\n", $data);
		$info = [
			'method' => $data[0],
			'fd' => $fd,
		];
		$info = json_encode($info);
		$this->send($serv, $fd, $info);
	}

	protected function send($serv, $fd, $info, $sendTimes = 1) {
		$client = $this->getClient()->get();
		if($sendTimes == 1){
			echo "获取client\n";
		}

		if ($client) {
			$ret = $client->send($info);//链接端口可能会出警告
			//无法判断tcp 因为应用层无法获得底层TCP连接的状态，执行send或recv时应用层与内核发生交互，才能得到真实的连接可用状态
			$rs = $client->recv();
			if ($rs) {
				$data = Response::getInstance()->write($rs);
				echo "发送数据: {$data}\n";
				$serv->send($fd, $data);
				$serv->close($fd);
				$this->getClient()->put($client);
				return true;
			}
			echo "rs为空??\n";
		}

		echo "client为空??\n";

		//否则就是出问题了，需要清理client, 然后自动重连
		if (($sendTimes > 1) || $this->atomic->lock()) {
			//重试1次，返回500状态错误
			$data = Response::getInstance()->error('server error');
			$serv->send($fd, $data);
			$serv->close($fd);
			if($sendTimes > 1){
				$this->atomic->unlock();
			}
			return false;
		}
		if($sendTimes == 1){
			echo "重新整？？\n";
			$this->getClient()->createPool();
			$this->send($serv, $fd, $info, 2);
		}
	}

	protected $client;

	/**
	 * @return HttpPool
	 */
	protected function getClient() {
		return HttpPool::getInstance(5);
	}

}