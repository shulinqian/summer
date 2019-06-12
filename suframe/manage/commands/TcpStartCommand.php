<?php

namespace suframe\manage\commands;

use suframe\core\components\Config;
use suframe\core\components\console\SymfonyStyle;
use suframe\core\components\event\EventManager;
use suframe\core\components\net\tcp\Server;
use suframe\core\components\net\tcp\Proxy;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TcpStartCommand extends TcpBase {

	/**
	 * @var SymfonyStyle
	 */
	protected $io;
	protected $config = [];
	/**
	 * @var Proxy
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
		$this->proxy = Proxy::getInstance();
		//守护进程运行
		if (true === $input->hasParameterOption(['--daemon', '-d'], true)) {
			$config['swoole']['daemonize'] = 1;
		}
		//创建启动服务
		$tcp->create($config);
		EventManager::get()->trigger('tcp.run.before', $this, $tcp);
		$server = $tcp->getServer();
		$server->on('receive', [$this, 'onReceive']);
		$server->on('start', [$this, 'onStart']);
		$server->on('shutdown', [$this, 'onShutdown']);
		$server->start();
		EventManager::get()->trigger('tcp.run.after', $this, $tcp);
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
	 * @param \Swoole\Server $server
	 * @param $fd
	 * @param $reactor_id
	 * @param $data
	 */
	public function onReceive(\Swoole\Server $server, $fd, $reactor_id, $data) {
	    if(!$data || !$fd){
	        return;
        }
        $request = \Zend\Http\Request::fromString($data);
        if($request->getUri() == '/favicon.ico'){
            return;
        }
        EventManager::get()->trigger('tcp.request', null, ['request' => &$request]);
		$out = $this->proxy->dispatch($server, $fd, $reactor_id, $request->toString());
        go(function () use ($request, $out){
            EventManager::get()->trigger('tcp.response.after', null, [
                'request' => $request,
                'out' => $out,
            ]);
        });
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