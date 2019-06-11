<?php

namespace suframe\manage\commands;

use suframe\core\components\Config;
use suframe\core\components\console\SymfonyStyle;
use suframe\core\event\EventManager;
use suframe\core\net\tcp\Server;
use suframe\manage\components\TcpProxy;
use suframe\manage\events\Events;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TcpStartCommand extends TcpBase {

	/**
	 * @var SymfonyStyle
	 */
	protected $io;
	protected $config = [];
	/**
	 * @var TcpProxy
	 */
	protected $proxy;

	/**
	 * 执行
	 * kill by shell: ps -ef |grep summer|cut -c 11-14 |xargs kill -9
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int|void|null
	 * @throws \Exception
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->io = $io = new SymfonyStyle($input, $output);
		$rs = $this->sendSig(0);
		if ($rs === true) {
			$io->warning('server has started');
			return;
		}
		$config = Config::getInstance();
		$tcp = new Server();
		$this->config = $config = $config->get('tcp')->toArray();
		//设置代理
		$this->proxy = TcpProxy::getInstance();
		//守护进程运行
		if (true === $input->hasParameterOption(['--daemon', '-d'], true)) {
			$config['swoole']['daemonize'] = 1;
		}
		//创建启动服务
		$tcp->create($config);
		EventManager::get()->trigger(Events::E_TCP_RUN_BEFORE, $this, $tcp);
		$server = $tcp->getServer();
		$server->on('receive', [$this, 'onReceive']);
		$server->on('start', [$this, 'onStart']);
		$server->on('shutdown', [$this, 'onShutdown']);
		$server->start();
		EventManager::get()->trigger(Events::E_TCP_RUN_AFTER, $this, $tcp);
	}

	/**
	 * 服务启动回调
	 */
	public function onStart() {
		$this->io->success('tcp server is running');
		$listen = $this->config['server']['listen'] == '0.0.0.0' ? '127.0.0.1' : $this->config['server']['listen'];
		$this->io->text('<info>open:</info> ' . $listen . ':' . $this->config['server']['port']);
	}

	/**
	 * 请求回调
	 * @param \Swoole\Server $serv
	 * @param $fd
	 * @param $reactor_id
	 * @param $data
	 */
	public function onReceive(\Swoole\Server $serv, $fd, $reactor_id, $data) {
		EventManager::get()->trigger('tcp.request', null, ['data' => $data]);
		$out = $this->proxy->dispatch($serv, $fd, $reactor_id, $data);
		EventManager::get()->trigger('tcp.response.after', null, [
			'out' => $out,
		]);
	}

	/**
	 * 服务结束
	 */
	public function onShutdown() {
		$this->io->writeln('tcp closed successfully');
	}

	/**
	 * 配置
	 */
	protected function configure() {
		$this->setName('tcp:start')
			->addOption('daemon', 'd', null, 'run server on the background');
	}

}