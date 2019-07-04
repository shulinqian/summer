<?php

namespace suframe\proxy;

use suframe\core\components\Config;
use suframe\core\traits\Singleton;
use suframe\proxy\driver\HttpDriver;
use suframe\proxy\driver\TcpDriver;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class App
{
    use Singleton;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $dispatch_type = Config::getInstance()->get('proxy.dispatch_type', 'http');
        switch ($dispatch_type){
            case 'http':
                HttpDriver::getInstance()->run($input, $output);
                break;
            default:
                TcpDriver::getInstance()->run($input, $output);
        }
    }

}