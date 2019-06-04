<?php
namespace suframe\manage;

use suframe\core\console\Config;
use suframe\core\console\Application;
use suframe\core\console\Console;
use suframe\core\traits\Singleton;

/**
 * summer framework manage
 * manage的主程序就是控制台，通过相应的command和controller完成功能
 * Class Core
 * @package suframe\manage
 */
class Core {
	use Singleton;

	protected $config;
	/**
	 * @var Application
	 */
	protected $console;

	/**
	 * 初始化
	 * @return $this
	 */
	public function init(){
		//初始化系统配置
		$this->config = $config = $this->getConfig();
		$config->load(__DIR__ . '/config/console.php');
		//应用自定义
		$selfConfig = SUMMER_APP_ROOT . 'config/console.php';
		if(is_file($selfConfig)){
			$config->load($selfConfig);
		}
		// 注册命令
		$this->console = $console = $this->getConsole();
		$console->getRouter()->setBlocked([]);
		// 注册独立命令
        // todo 暂时还没有设计单独的命令
//		$console->registerCommands('suframe\\manage\\commands', __DIR__ . '/commands');
		// 注册命令组
		$console->registerGroups('suframe\\manage\\controllers', __DIR__ . '/controllers');
		return $this;
	}

	/**
	 * 控制台启动
	 * @param array $args
	 * @return void
	 * @throws \ReflectionException
	 */
	public function run(array $args): void {
		$command = array_shift($args);
		$console = $this->console;
		switch ($command){
			case '':
				$console->showHelpInfo();
				break;
			case 'help':
			case 'list':
				$console->showCommandList();
				break;
			case 'version':
				$console->showVersionInfo();
				break;
			default:
				$console->run();
		}
	}

	/**
	 * 获取配置
	 * @return Config
	 */
	public function getConfig(): Config {
		if(!$this->config){
			$this->config = new Config;
		}
		return $this->config;
	}

	/**
	 * 设置配置
	 * @param Config $config
	 */
	public function setConfig(Config $config): void {
		$this->config = $config;
	}

	/**
	 * 获取console
	 * @return Application
	 */
	public function getConsole(): Application {
		if(!$this->console){
			$this->console = Console::getInstance()->getApp($this->getConfig()->get('console'));
		}
		return $this->console;
	}

	/**
	 * 设置console
	 * @param Application $console
	 */
	public function setConsole(Application $console): void {
		$this->console = $console;
	}

}