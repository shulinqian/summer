<?php

namespace suframe\manage\commands;


use suframe\core\components\Config;
use suframe\core\components\console\SymfonyStyle;
use suframe\core\event\EventManager;
use suframe\core\net\http\Server;
use suframe\manage\components\HttpProxy;
use suframe\manage\events\Events;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HttpStartCommand extends HttpBase
{

    /**
     * @var SymfonyStyle
     */
    protected $io;
    protected $httpConfig = [];
    /**
     * kill by shell: ps -ef |grep summer|cut -c 11-14 |xargs kill -9
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = $io = new SymfonyStyle($input, $output);

        $rs = $this->sendSig(0);
        if($rs === true){
            $io->warning('server has started');
            return;
        }

        $config = Config::getInstance();
        $http = new Server();
        $this->httpConfig = $httpConfig = $config->get('http')->toArray();

        //守护进程运行
        if (true === $input->hasParameterOption(['--daemon', '-d'], true)) {
            $httpConfig['swoole']['daemonize'] = 1;
        }
        $http->create($httpConfig);
        EventManager::get()->trigger(Events::E_HTTP_RUN_BEFORE, $this, $http);
        $server = $http->getServer();
        $server->on('receive', [$this, 'onReceive']);
        $server->on('start', [$this, 'onStart']);
        $server->on('shutdown', [$this, 'onShutdown']);

        HttpProxy::getInstance();
        $http->start();
        EventManager::get()->trigger(Events::E_HTTP_RUN_AFTER, $this, $http);
    }

    /**
     * 服务启动回调
     */
    public function onStart(){
        $this->io->success('http server is running');
        $listen = $this->httpConfig['server']['listen'] == '0.0.0.0' ? '127.0.0.1' : $this->httpConfig['server']['listen'];
        $this->io->text('<info>open:</info> ' . $listen . ':' . $this->httpConfig['server']['port']);
    }

    /**
     * 请求回调
     * @param Request $request
     * @param Response $response
     */
    public function onReceive(\Swoole\Server $serv, $fd, $reactor_id, $data) {
		HttpProxy::getInstance()->dispatch($serv, $fd, $reactor_id, $data);
        return;
        $uri = $request->server['request_uri'];
        if ($uri == '/favicon.ico') {
            $response->status(404);
            $response->end();
            return;
        }
        EventManager::get()->trigger('http.request', null, $request);
        $out = HttpProxy::getInstance()->dispatch($request);
        //$out = "<h1>Hello Swoole . #" . rand(1000, 9999) . "</h1>"; //业务处理

        EventManager::get()->trigger('http.response.before', null, ['out' => $out]);
        $response->end($out);
        EventManager::get()->trigger('http.response.after', null, [
            'request' => $request,
            'response' => $response,
            'out' => $out,
        ]);
    }

    /**
     * 服务结束
     */
    public function onShutdown(){
        $this->io->writeln('Http closed successfully');
    }

    /**
     * 配置
     */
    protected function configure()
    {
        $this->setName('http:start')
            ->addOption('daemon', 'd', null, 'run server on the background')
        ;
    }

}