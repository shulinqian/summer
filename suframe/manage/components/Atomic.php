<?php
/**
 * User: qian
 * Date: 2019/6/5 16:22
 */
namespace suframe\manage\components;

use suframe\core\traits\Singleton;

class Atomic {

	use Singleton;

	protected $atomic;
	public function __construct() {
		$this->atomic = new \Swoole\Atomic(0);
	}

	public function requestId(){
		$id = $this->atomic->add(1);
		if($id > 99999999){
			//防止溢出
			$this->atomic->set(1);
		}
		return $id;
	}

}