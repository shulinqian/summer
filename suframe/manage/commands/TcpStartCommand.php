<?php

namespace suframe\manage\commands;

use suframe\core\components\Config;
use suframe\core\components\console\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TcpStartCommand extends TcpBase {

	/**
	 * 执行
	 * kill by shell: ps -ef |grep summer|cut -c 11-14 |xargs kill -9
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int|void|null
	 * @throws \Exception
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
	    $this->customServer($input);

		$rs = $this->sendSig(0);
		if ($rs === true) {
            (new SymfonyStyle($input, $output))->warning('server has started');
			return;
		}

		$appClass = Config::getInstance()->get('console.app');
		if(!$appClass){
            (new SymfonyStyle($input, $output))->error('app class not found');
            return;
        }
		/** @var \suframe\manage\App $app */
        $app = new $appClass;
        $app->run($input, $output);
	}

    /**
     * 命令行自定server
     * @param InputInterface $input
     */
	protected function customServer(InputInterface $input){
	    $config = Config::getInstance();
        if ($host = $input->hasParameterOption(['--host', '-H'])) {
            $config['tcp']['server']['host'] = $host;
        }
        if ($port = $input->getParameterOption(['--port', '-P'])) {
            $config['tcp']['server']['port'] = $port;
            $config['tcp']['swoole']['log_file'] .= ".{$port}.log";
            $config['tcp']['swoole']['pid_file'] .= ".{$port}.pid";
        }
    }

	/**
	 * 配置
	 */
	protected function configure() {
		$this->setName('tcp:start')
			->addOption('host', 'H', InputOption::VALUE_OPTIONAL, 'custom host')
			->addOption('port', 'P', InputOption::VALUE_OPTIONAL, 'custom port')
			->addOption('daemon', 'd', null, 'run server on the background');
	}

}