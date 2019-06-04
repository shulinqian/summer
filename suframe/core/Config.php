<?php
namespace suframe\core;

use Noodlehaus\ConfigInterface;
use suframe\core\traits\Singleton;

/**
 * WorkerFee
 * @package app\api\\job
 * @method get($key, $default = null)
 * @method set($key, $value)
 * @method has($key)
 * @method merge(ConfigInterface $config)
 * @method all()
 */
class Config{
	use Singleton;

	/**
	 * @var \Noodlehaus\Config
	 */
	protected $entity;

	/**
	 * @param $file
	 * @return \Noodlehaus\Config
	 */
	public function load($file){
		$config = new \Noodlehaus\Config($file);
		if($this->entity){
			$this->entity->merge($config) ;
		} else {
			$this->entity = $config;
		}
		return $this->entity;
	}

	public function __call($name, $arguments) {
		// TODO: Implement __call() method.
		return $this->entity->$name(...$arguments);
	}

	/**
	 * @return \Noodlehaus\Config
	 */
	public function getEntity(): \Noodlehaus\Config {
		return $this->entity;
	}

}