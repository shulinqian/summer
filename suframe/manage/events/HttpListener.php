<?php
/**
 * User: qian
 * Date: 2019/6/5 13:17
 */

namespace suframe\manage\events;

use suframe\manage\components\Atomic;
use Swoole\Http\Request;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;

class HttpListener implements ListenerAggregateInterface {
	use ListenerAggregateTrait;

	/**
	 * 注册事件
	 * @param EventManagerInterface $events
	 * @param int $priority
	 */
	public function attach(EventManagerInterface $events, $priority = 1) {
		$this->listeners[] = $events->attach(Events::E_HTTP_REQUEST, [$this, 'request'], $priority);
	}

	/**
	 * 请求事件
	 * @param EventInterface $e
	 */
	public function request(EventInterface $e) {
		/** @var Request $request */
		$request = $e->getParams();
		//生成唯一请求id
		$id = Atomic::getInstance()->requestId();
		$request->header['request-id'] = $id;
		echo "新请求，通过事件" .$e->getName(), '访问id:'. $request->header['request-id'], "\n";
//		echo $request->['request_time_float'] , "\n";
	}
}