<?php
/**
 * User: qian
 * Date: 2019/6/10 10:12
 */

namespace suframe\manage\components;

use suframe\core\traits\Singleton;
use Swoole\Client;
use Swoole\Coroutine\Channel;

/**
 * http连接池
 * Class HttpPool
 * @package suframe\manage\components
 */
class HttpPool {
	use Singleton;

	protected $maxReTry = 200;
	protected $timeout = 0.1;

	/**
	 * @var Channel
	 */
	protected $pool;
	protected $size;

	public function __construct($size) {
		$this->size = $size;
		$this->createPool($size);
	}

	public function createPool($size = null) {
		$this->creating = true;
		$size = $this->size;
		if ($this->pool) {
			while ($rs = $this->get()) {
				//清理无效连接
				$rs->isConnected() && $rs->close();
			}
			echo "clean pool\n";
		} else {
			$this->pool = new Channel($size);
		}
		for ($i = 0; $i < $size; $i++) {
			$client = new Client(SWOOLE_SOCK_TCP | SWOOLE_KEEP);
			$res = @$client->connect('127.0.0.1', 9502);
			if ($res) {
				$this->put($client);
			}
		}

	}

	public function put($client) {
		$this->pool->push($client);
	}

	public function getLength() {
		return $this->pool->length();
	}

	/**
	 * @return Client|null
	 */
	public function get() {
		return $this->pool->pop($this->timeout);
	}

}