<?php

namespace suframe\register;

use suframe\core\components\Config;
use suframe\core\components\console\SymfonyStyle;
use suframe\core\components\event\EventManager;
use suframe\core\components\net\http\Server;
use suframe\core\traits\Singleton;
use suframe\register\components\Proxy;
use Swoole\Http\Request;
use Swoole\Http\Response;
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
        $server->on('request', [$this, 'onRequest']);
        $server->on('start', [$this, 'onStart']);
        $server->on('shutdown', [$this, 'onShutdown']);
        $server->start();
        EventManager::get()->trigger('tcp.run.after', $this, $tcp);
    }

    /**
     * 服务启动回调
     */
    public function onStart() {
        $this->io->success('ra server is running');
        $ip = swoole_get_local_ip();
        $listen = $this->config['server']['listen'] == '0.0.0.0' ? array_shift($ip) : $this->config['server']['listen'];
        $this->io->text('<info>ra server:</info> ' . $listen . ':' . $this->config['server']['port']);
    }

    /**
     * tcp请求回调
     * @param \Swoole\Server $server
     * @param $fd
     * @param $reactor_id
     * @param $data
     */
    public function onRequest(Request $request, Response $response) {
        var_dump($request->get);

        $cli = new \Swoole\Coroutine\Http\Client('127.0.0.1', 9501);
        $cli->post('/summer', array("command" => 'UPDATE_SERVERS'));
        $rs = $cli->body;
        var_dump($rs);
        $cli->close();

        if($request->get === null){
            $response->status(404);
            $response->write('Not Found');
            return false;
        }
        EventManager::get()->trigger('tcp.request', $this, ['request' => &$request]);
        $out = $this->proxy->dispatch($request);
        $response->write($out);
        go(function () use ($request, $out){
            EventManager::get()->trigger('tcp.response.after', $this, [
                'request' => $request,
                'out' => $out,
            ]);
        });
    }

    /**
     * 服务结束
     */
    public function onShutdown() {
        EventManager::get()->trigger('tcp.shutDown', $this);
    }

	protected function initProxy(){
		//这里应该是action代理
		$this->proxy = new Proxy();
	}

}