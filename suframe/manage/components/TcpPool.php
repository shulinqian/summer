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
 * tcp连接池
 * Class TcpPool
 * @package suframe\manage\components
 */
class TcpPool {
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
        $this->createPool();
	}

	public function createPool($size = null) {
		$this->creating = true;
		$size = $this->size;
        $this->pool = new Channel($size);
	}

	public function put($client) {
	    if($this->pool->length() > $this->size){
            $client->close();
	        return false;
        }
		$this->pool->push($client);
	}

	public function getLength() {
		return $this->pool->length();
	}

	/**
	 * @return Client|null
	 */
	public function get() {
//        echo "连接数" . $this->pool->length() . "\n";
        if($this->pool->length()){
            return $this->pool->pop($this->timeout);
        }
        $client = new Client(SWOOLE_SOCK_TCP | SWOOLE_KEEP);
        $res = @$client->connect('127.0.0.1', 9502);
        if ($res) {
            $this->put($client);
            return $this->get();
        }
	}

}