<?php
/**
 * User: qian
 * Date: 2019/6/5 11:42
 */
namespace suframe\core\components\net\tcp;

use Zend\Http\Request;

/**
 * tcp 代理
 * Class HttpProxy
 * @package suframe\manage\components
 */
class Proxy {
	protected $counter = 0;
	protected $resultError = 0;
	protected $pools;
	protected $config;

	public function __construct($config) {
		$this->config = $config;
		$this->initPools();
	}

	protected function initPools(){
		foreach ($this->config as $item) {
			$this->addPool($item);
		}
	}

	public function dispatch(Request $request) {
		$pool = $this->getPool($request->getUri()->getPath());
		if($pool){
			$client = $pool->get();
			if ($client) {
				//链接端口可能会出警告
				$ret = @$client->send($request . '');
				if($ret){
					//无法判断tcp 因为应用层无法获得底层TCP连接的状态，执行send或recv时应用层与内核发生交互，才能得到真实的连接可用状态
					$rs = $client->recv();
					if ($rs) {
						$pool->put($client);
						return $rs;
					}
				}
				$client->close();
			}
		}
        return null;
	}

	/**
	 * @param $uri
	 * @return bool|Pool
	 */
	protected function getPool($path){
		if(!$path){
			return false;
		}
		foreach ($this->pools as $poolPath => $item) {
			if($poolPath == $path){
			    $key = array_rand($item);
				return $item[$key];
			}
		}
		$path = explode('/', $path);
		array_pop($path);
		$path = implode('/', $path);
		return $this->getPool($path);
	}

	/**
	 * @return mixed
	 */
	public function getPools() {
		return $this->pools;
	}

	/**
	 * 动态增加连接池
	 * @param $config
	 * @return bool
	 */
	public function addPool($config){
        $key = md5($config['host'] . ':' . $config['port']);
        $path = $config['path'];
        if(!isset($this->pools[$path])){
            $this->pools[$path] = [];
        }
        if(isset($this->pools[$path][$key]) && $this->pools[$path][$key]){
            return false;
        }
        $this->pools[$path][$key] = new Pool($config['host'], $config['port'], $config['size'] ?? 1, $config['overflowMax'] ?? null);
	}

	/**
	 * 动态删除连接池
	 * @param $name
	 */
	public function removePool($path, $host, $port){
        $key = md5($host . ':' . $port);
		if(isset($this->pools[$path]) && isset($this->pools[$path][$key])){
			while ($client = $this->pools[$path][$key]->get()){
				$client->close();
			}
			unset($this->pools[$path][$key]);
		}
	}

}