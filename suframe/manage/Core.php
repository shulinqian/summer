<?php
namespace suframe\manage;

use suframe\core\console\Console;
use suframe\core\traits\Singleton;

class Core {
	use Singleton;

	protected $command;
	function __construct() {
		//初始化系统配置

		//注册命令
		$console = Console::getInstance()->getApp();
	}

	public function config(){

	}

	public function run(array $args){
		$command = array_shift($args);
		if(empty($command)){
			$command = 'help';
		}
		return '';
	}

}