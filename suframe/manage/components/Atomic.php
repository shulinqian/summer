<?php
/**
 * User: qian
 * Date: 2019/6/5 16:22
 */
namespace suframe\manage\components;

use suframe\core\traits\Singleton;
use swoole_lock;

class Atomic {

	use Singleton;

	protected $atomic;
	public function __construct() {
		$this->atomic = new \Swoole\Atomic(0);
	}

	public function requestId(){
		return $this->atomic->add(1);
	}

}