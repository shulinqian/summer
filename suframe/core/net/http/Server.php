<?php

namespace suframe\core\net\http;

use suframe\core\event\EventManager;
use Swoole\Http\Request;
use Swoole\Http\Response;

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
		$this->server = new \Swoole\Http\Server($config['server']['listen'], $config['server']['port']);
		$this->onRequest();
		$this->set($config['swoole']);
		return $this->server;
	}

	protected function onRequest() {
		$this->server->on('request', function (Request $request, Response $response) {
			$uri = $request->server['request_uri'];
			if ($uri == '/favicon.ico') {
				$response->status(404);
				$response->end();
				return;
			}
			EventManager::get()->trigger('http.request', null, $request);
			$out = "<h1>Hello Swoole . #" . rand(1000, 9999) . "</h1>"; //业务处理

			EventManager::get()->trigger('http.response.before', null, ['out' => $out]);
			$response->end($out);
			EventManager::get()->trigger('http.response.after', null, [
				'request' => $request,
				'response' => $response,
				'out' => $out,
			]);
		});
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
	 * @return \Swoole\Http\Server
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