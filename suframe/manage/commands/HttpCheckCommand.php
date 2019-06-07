<?php

namespace suframe\manage\commands;


use suframe\core\components\Config;
use suframe\core\components\console\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HttpCheckCommand extends HttpBase {

    /**
     * kill by shell: ps -ef |grep summer|cut -c 11-14 |xargs kill -9
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rs = $this->sendSig(0);
        $io = new SymfonyStyle($input, $output);
        if($rs !== true){
            $io->note('http has closed');
            return;
        }

        $httpConfig = Config::getInstance()->get('http.server')->toArray();
        $io->success(sprintf('http is running, listen: %s:%s', $httpConfig['listen'], $httpConfig['port']));
    }

    protected function configure()
    {
        $this->setName('http:check');
    }

}