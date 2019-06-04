<?php
/**
 * User: qian
 * Date: 2019/6/4 12:57
 */

namespace suframe\manage\controllers;

use Inhere\Console\Util\Show;
use suframe\core\config\Config;
use suframe\core\console\Controller;
use suframe\core\net\http\Server;
use suframe\manage\Core;

/**
 * 帮助命令
 * Class Help
 * @package suframe\manage\commands
 */
class Http extends Controller {

	protected static $name = 'http';

	protected static $description = 'summer manage http';

    protected $port = 9501;
    protected $listen = '0.0.0.0';

	/**
	 * http start
	 * @usage {command} [arg ...] [--opt ...]
	 * @options
	 *  -d            run server on the background
	 * @example
	 *  bin/summer.php http:start
	 *  bin/summer.php http:start -d
	 */
	public function startCommand() {
        $config = $this->getConfig();
        $this->showCommand($config);
        $http = new Server();
        $http->create();
        $http->start();
	}

	protected function showCommand(Config $config){
        $consoleConfig = Core::getInstance()->getConfig();
        $data = [
            'http' => $config->get('http.listen') . ':' . $config->get('http.port'),
            'version' => $consoleConfig->get('console.version'),
        ];
        Show::panel($data, $consoleConfig->get('console.name'));
    }

	protected function getConfig(){
        $config = new Config([]);
        $config->mergeData(['http' => [
            'port' => $this->port,
            'listen' => $this->listen,
        ]]);

        $httpConfig = SUMMER_APP_ROOT . 'config/http.php';
        $config->mergeFile($httpConfig);
        return $config;
    }
}