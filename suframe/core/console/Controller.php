<?php
namespace suframe\core\console;

abstract class Controller extends \Inhere\Console\Controller{

	public $disableGlobalOptions = true;
	protected function beforeRenderCommandHelp(array &$help): void
	{
		//这个全局显示命令很烦人
		if($this->disableGlobalOptions && isset($help['Global Options:'])){
			unset($help['Global Options:']);
		}
	}

}