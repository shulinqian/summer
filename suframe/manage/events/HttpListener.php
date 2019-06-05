<?php
/**
 * User: qian
 * Date: 2019/6/5 13:17
 */

namespace suframe\manage\events;


use suframe\manage\components\Atomic;
use suframe\manage\components\Table;
use suframe\manage\Core;
use Swoole\Http\Request;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;

class HttpListener implements ListenerAggregateInterface {
	use ListenerAggregateTrait;

	/**
	 * Attach one or more listeners
	 *
	 * Implementors may add an optional $priority argument; the EventManager
	 * implementation will pass this to the aggregate.
	 *
	 * @param EventManagerInterface $events
	 * @param int $priority
	 * @return void
	 */
	public function attach(EventManagerInterface $events, $priority = 1) {
		$this->listeners[] = $events->attach(Events::E_HTTP_REQUEST, [$this, 'request'], $priority);
		$this->listeners[] = $events->attach(Events::E_HTTP_RESPONSE_BEFORE, [$this, 'responseBefore'], $priority);
		$this->listeners[] = $events->attach(Events::E_HTTP_RESPONSE_AFTER, [$this, 'responseAfter'], $priority);
	}

	public function request(EventInterface $e) {
		/** @var Request $request */
		$request = $e->getParams();
		//生成唯一请求id
		$id = Atomic::getInstance()->requestId();
		$request->header['request-id'] = $id;
		echo "新请求，通过事件" .$e->getName(), '访问id:'. $request->header['request-id'], "\n";
//		echo $request->['request_time_float'] , "\n";
	}

	public function responseBefore(EventInterface $e) {

	}

	public function responseAfter(EventInterface $e) {

	}


	/**
	 * Detach all previously attached listeners
	 *
	 * @param EventManagerInterface $events
	 * @return void
	 */
	public function detach(EventManagerInterface $events) {
		// TODO: Implement detach() method.
	}
}