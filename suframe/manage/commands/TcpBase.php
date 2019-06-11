<?php


namespace suframe\manage\commands;

use suframe\core\components\Config;
use Symfony\Component\Console\Command\Command;
use swoole_process;

abstract class TcpBase extends Command
{

    protected function sendSig($sig){
        $config = Config::getInstance();
        $pidFile = $config->get('tcp.swoole.pid_file');
        if(!is_file($pidFile)){
            return 'no pid file';
        }
        $pid = file_get_contents($pidFile);
        if (!@swoole_process::kill($pid, $sig)) {
            return 'no process';
        }
        return true;
    }

}