<?php

namespace suframe\core\net\tcp;

class Server {

	/**
	 * @var \Swoole\Http\Server
	 */
	protected $server;

	/**
	 * @param array $config
	 * @return \Swoole\Http\Server
	 */
	public function create(array $config) {
		$this->server = $server = new \Swoole\Server($config['server']['listen'], $config['server']['port'], SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
		$this->set($config['swoole']);
		return $this->server;
	}

	/**
	 * @throws \Exception
	 */
	public function set(array $setting) {
		$this->getServer()->set($setting);
	}

	/**
	 * @throws \Exception
	 */
	public function start() {
		$this->getServer()->start();
	}

	/**
	 * @return \Swoole\Server
	 * @throws \Exception
	 */
	public function getServer() {
		if (!$this->server) {
			throw new \Exception('please create server');
		}
		return $this->server;
	}

	/**
	 * @param mixed $server
	 */
	public function setServer($server) {
		$this->server = $server;
	}

}