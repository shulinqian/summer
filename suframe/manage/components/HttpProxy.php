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
	protected $counter = 0;
	protected $resultError = 0;

	public function __construct() {
		$this->getClient();
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

	protected $client = [];
	protected function send($serv, $fd, $info) {
		$client = $this->getClient()->get();
		if ($client) {
			$ret = $client->send($info);//链接端口可能会出警告
            if(!$ret){
                echo "数据都没有发送出去\n";
            }
			//无法判断tcp 因为应用层无法获得底层TCP连接的状态，执行send或recv时应用层与内核发生交互，才能得到真实的连接可用状态
			$rs = $client->recv();
			if ($rs) {
			    $this->getClient()->put($client);
                echo "\n\n" . $info . $rs, "\n\n";
				$data = Response::getInstance()->write($info . $rs);
//				echo "发送数据: {$data}\n";
				$serv->send($fd, $data);
				$serv->close($fd);
				return true;
			} else {
			    //无效
                $client->close();
            }
//			echo "rs为空??\n";
		}
        $data = Response::getInstance()->error('server error');
        $serv->send($fd, $data);
        $serv->close($fd);
	}

	/**
	 * @return HttpPool
	 */
	protected function getClient() {
		return HttpPool::getInstance(1);
	}

}