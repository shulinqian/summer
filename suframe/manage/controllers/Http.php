<?php
/**
 * User: qian
 * Date: 2019/6/4 12:57
 */

namespace suframe\manage\controllers;

use suframe\core\console\Controller;

/**
 * 帮助命令
 * Class Help
 * @package suframe\manage\commands
 */
class Http extends Controller {

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
	 */
	public function startCommand() {
		$this->write('hello, welcome!! this is ' . __METHOD__);
	}
}