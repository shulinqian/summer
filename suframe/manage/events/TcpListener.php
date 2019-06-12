<?php
/**
 * User: qian
 * Date: 2019/6/5 13:17
 */

namespace suframe\manage\events;

use Swoole\Coroutine;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\Request;

class TcpListener implements ListenerAggregateInterface {
	use ListenerAggregateTrait;

	/**
	 * 注册事件
	 * @param EventManagerInterface $events
	 * @param int $priority
	 */
	public function attach(EventManagerInterface $events, $priority = 1) {
		$this->listeners[] = $events->attach('tcp.request', [$this, 'request'], $priority);
//		$this->listeners[] = $events->attach(Events::E_TCP_RESPONSE_AFTER, [$this, 'after'], $priority);
	}

	/**
	 * 请求事件
	 * @param EventInterface $e
	 */
	public function request(EventInterface $e) {
	    /** @var Request $request */
        $request = $e->getParam('request');
        $headers = $request->getHeaders();
        //暂时用最简单的方案生成
        $headers->addHeaderLine('request_id', session_create_id());
//        $headers->addHeaderLine('uid', rand(1, 9999));

	}

	/**
	 * 返回事件
	 * @param EventInterface $e
	 */
	public function after(EventInterface $e) {
		$data = $e->getParams();
		echo "response data:\n {$data['out']}\n";
	}
}