<?php
/**
 * User: qian
 * Date: 2019/6/5 16:47
 */

namespace suframe\manage\components;


use suframe\core\traits\Singleton;
use swoole_table;

class Table {
	use Singleton;

	/**
	 * @var \Swoole\Table
	 */
	protected $table;
	public function __construct() {
		$this->table = new \Swoole\Table(100);
		$this->table->column('id', swoole_table::TYPE_INT, 8);
		$this->table->create();
		$this->table->set('request-id', ['id' => 1]);
	}

	public function requestId(){
		$id = $this->table->incr('request-id', 'id');
		if($id > 99999999){
			$this->table->set('request-id', ['id' => 1]);
		}
		return $id;
	}
}