<?php

namespace suframe\core\net\http;

/**
 * Class Server
 * @method \Swoole\Http\Server getServer()
 * @package suframe\core\net\http
 */
class Server extends \suframe\core\net\tcp\Server {

	/**
	 * @var \Swoole\Http\Server
	 */
	protected $server;

	/**
	 * @param array $config
	 * @return \Swoole\Http\Server
	 */
	public function create(array $config) {
		$this->server = $server = new \Swoole\Http\Server($config['server']['listen'], $config['server']['port']);
		$this->set($config['swoole']);
		return $this->server;
	}

}