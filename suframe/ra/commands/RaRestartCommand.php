<?php
namespace suframe\ra\commands;

use suframe\core\components\console\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RaRestartCommand extends RaStartCommand
{

    protected function execute(InputInterface $input, OutputInterface $output){
        $rs = $this->sendSig(SIGUSR1);
        $io = new SymfonyStyle($input, $output);
        if($rs !== true){
            $io->error($rs);
        } else {
            $io->success('tcp restart success');
        }
    }

    /**
     * 配置
     */
    protected function configure()
    {
        $this->setName('tcp:restart');
    }

}