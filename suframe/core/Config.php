<?php
namespace suframe\core;

use suframe\core\traits\Singleton;

class Config{
	use Singleton;
	protected $type;

	protected $entity = [];
	public function getEntity($type){
		if(!isset($this->entity[$type])){
			$this->entity[$type] = new \Noodlehaus\Config(__DIR__ . '/config/config.php');
		}
		return $this->entity[$type];
	}

}