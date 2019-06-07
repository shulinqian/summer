<?php

namespace suframe\manage\commands;


use suframe\core\components\Config;
use suframe\core\components\console\SymfonyStyle;
use suframe\core\event\EventManager;
use suframe\core\net\http\Server;
use suframe\manage\events\Events;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HttpStartCommand extends HttpBase
{

    /**
     * kill by shell: ps -ef |grep summer|cut -c 11-14 |xargs kill -9
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $rs = $this->sendSig(0);
        if($rs === true){
            $io->warning('server has started');
            return;
        }

        $config = Config::getInstance();
        $http = new Server();
        $httpConfig = $config->get('http')->toArray();

        //守护进程运行
        if (true === $input->hasParameterOption(['--daemon', '-d'], true)) {
            $httpConfig['swoole']['daemonize'] = 1;
        }
        $http->create($httpConfig);
        EventManager::get()->trigger(Events::E_HTTP_RUN_BEFORE, $this, $http);

        $io->success('http server is running');
        $listen = $httpConfig['server']['listen'] == '0.0.0.0' ? '127.0.0.1' : $httpConfig['server']['listen'];
        $io->text('<info>open:</info> ' . $listen . ':' . $httpConfig['server']['port']);

        $http->start();
        EventManager::get()->trigger(Events::E_HTTP_RUN_AFTER, $this, $http);
    }

    protected function configure()
    {
        $this->setName('http:start')
        ->addOption('daemon', 'd', null, 'run server on the background')
        ;
    }

}