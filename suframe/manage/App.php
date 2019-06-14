<?php

namespace suframe\manage;

use suframe\core\components\Config;
use suframe\core\components\console\SymfonyStyle;
use suframe\core\components\event\EventManager;
use suframe\core\components\net\tcp\Proxy;
use suframe\core\components\net\tcp\Server;
use suframe\core\traits\Singleton;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class App
{
    use Singleton;

    protected $config = [];
    /**
     * @var SymfonyStyle
     */
    protected $io;
    /**
     * @var Proxy
     */
    protected $proxy;

    /**
     * @throws \Exception
     */
    public function run(InputInterface $input, OutputInterface $output) {
        $this->io = new SymfonyStyle($input, $output);
        $config = Config::getInstance();
        $tcp = new Server();
        $this->config = $config->get('tcp')->toArray();
        //设置代理
        $this->initProxy();
        //守护进程运行
        if (true === $input->hasParameterOption(['--daemon', '-d'], true)) {
            $this->config['swoole']['daemonize'] = 1;
        }

        //创建启动服务
        $tcp->create($this->config);
        EventManager::get()->trigger('tcp.run.before', $this, $tcp);
        $server = $tcp->getServer();
        //转发类型，http需要拆包过滤
        $dispatch_type = $this->config['dispatch_type'] ?? 'http';
        $server->on('receive', [$this, 'onReceive' . ucfirst($dispatch_type)]);
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
        $ip = swoole_get_local_ip();
        $listen = $this->config['server']['listen'] == '0.0.0.0' ? array_shift($ip) : $this->config['server']['listen'];
        $this->io->text('<info>open:</info> ' . $listen . ':' . $this->config['server']['port']);
    }

    /**
     * http请求回调
     * @param \Swoole\Server $server
     * @param $fd
     * @param $reactor_id
     * @param $data
     */
    public function onReceiveHttp(\Swoole\Server $server, $fd, $reactor_id, $data) {
        if(!$data || !$fd){
            return;
        }
        $request = \Zend\Http\Request::fromString($data);
        if($request->getUri() == '/favicon.ico'){
            return;
        }
        EventManager::get()->trigger('http.request', null, ['request' => &$request]);
        $out = $this->proxy->dispatch($server, $fd, $reactor_id, $request);
        go(function () use ($request, $out){
            EventManager::get()->trigger('http.response.after', null, [
                'request' => $request,
                'out' => $out,
            ]);
        });
    }

    /**
     * tcp请求回调
     * @param \Swoole\Server $server
     * @param $fd
     * @param $reactor_id
     * @param $data
     */
    public function onReceiveTcp(\Swoole\Server $server, $fd, $reactor_id, $data) {
        EventManager::get()->trigger('tcp.request', null, ['data' => &$data]);
        $out = $this->proxy->dispatch($server, $fd, $reactor_id, $data);
        go(function () use ($data, $out){
            EventManager::get()->trigger('tcp.response.after', null, [
                'request' => $data,
                'out' => $out,
            ]);
        });
    }

    /**
     * 服务结束
     */
    public function onShutdown() {
        EventManager::get()->trigger('tcp.shutDown', null, ['request' => &$request]);
    }

    protected function initProxy(){
    	$config = [
    		[
    			'name' => 'news',
    			'path' => '/news',
				'host' => '127.0.0.1',
				'port' => 9502,
			],
    		[
    			'name' => 'goods',
    			'path' => '/goods',
				'host' => '127.0.0.1',
				'port' => 9602,
			],
		];
		$this->proxy = new Proxy($config);
	}

	/**
	 * @return Proxy
	 */
	public function getProxy(): Proxy {
		return $this->proxy;
	}

}