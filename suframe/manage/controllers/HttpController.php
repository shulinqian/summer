<?php
/**
 * User: qian
 * Date: 2019/6/4 12:57
 */

namespace suframe\manage\controllers;

use Inhere\Console\Util\Show;
use suframe\core\components\Config;
use suframe\core\console\Controller;
use suframe\core\net\http\Server;
use suframe\core\event\EventManager;
use suframe\manage\Core;
use suframe\manage\events\Events;
use swoole_process;

/**
 * http命令
 * Class Help
 * @package suframe\manage\commands
 */
class HttpController extends Controller {

	protected static $name = 'http';
	protected static $description = 'summer manage http';

	/**
	 * http start
	 * @usage {command} [arg ...] [--opt ...]
	 * @options
	 *  -d            run server on the background
	 * @example
	 *  bin/summer.php http:start
	 *  bin/summer.php http:start -d
	 * @throws \Exception
	 */
	public function startCommand() {
        $config = $this->getConfig();
        $this->showCommand();
        $http = new Server();
        $httpConfig = $config->get('http')->toArray();
        //守护进程运行
		//ps -ef |grep summer|cut -c 11-14 |xargs kill -9
        if($this->getOpt('d')){
			$httpConfig['swoole']['daemonize'] = 1;
		}
        $http->create($httpConfig);
        EventManager::get()->trigger(Events::E_HTTP_RUN_BEFORE, $this, $http);
        $http->start();
		EventManager::get()->trigger(Events::E_HTTP_RUN_AFTER, $this, $http);
	}

	/**
	 * http stop
	 * @usage {command} [arg ...] [--opt ...]
	 * @options
	 *  -s            https://wiki.swoole.com/wiki/page/158.html
	 * @example
	 *  bin/summer.php http:stop
	 *  bin/summer.php http:stop -s
	 * @throws \Exception
	 */
	public function stopCommand(){
		$sig = $this->getOpt('s', SIGTERM);
		$this->sendSig($sig, 'process has stop');
	}

	/**
	 * http check
	 * @usage {command} [arg ...]
	 * @example
	 *  bin/summer.php http:check
	 */
	public function checkCommand(){
		$this->sendSig(0, 'server is running');
	}

	protected function sendSig($sig, $msg){
		$config = $this->getConfig();
		$output = Core::getInstance()->getOutput();
		$pidFile = $config->get('http.swoole.pid_file');
		if(!is_file($pidFile)){
			return $output->writeln('no process');
		}
		$pid = file_get_contents($pidFile);
		if (!swoole_process::kill($pid, $sig)) {
			return $output->writeln('no process');
		}
		return $output->colored($msg);
	}

	protected function showCommand(){
		$config = Config::getInstance();
        $data = [
            'http' => $config->get('http.server.listen') . ':' . $config->get('http.server.port'),
            'version' => $config->get('console.version'),
        ];
        Show::panel($data, $config->get('console.name'));
    }

	protected function getConfig(){
        $config = Config::getInstance();
		$config->loadFileByName(__DIR__ . '/../config/http.php');
		$config->loadFileByName(SUMMER_APP_ROOT . 'config/http.php');
        return $config;
    }
}