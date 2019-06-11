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

class TcpListener implements ListenerAggregateInterface {
	use ListenerAggregateTrait;

	/**
	 * 注册事件
	 * @param EventManagerInterface $events
	 * @param int $priority
	 */
	public function attach(EventManagerInterface $events, $priority = 1) {
		$this->listeners[] = $events->attach(Events::E_TCP_REQUEST, [$this, 'request'], $priority);
		$this->listeners[] = $events->attach(Events::E_TCP_RESPONSE_AFTER, [$this, 'after'], $priority);
	}

	/**
	 * 请求事件
	 * @param EventInterface $e
	 */
	public function request(EventInterface $e) {
		$data = $e->getParams();
        $request = \Zend\Http\Request::fromString($data['data']);
        if($request->getUri() == '/favicon.ico'){
            $data['data'] = null;
            return;
        }
        $headers = $request->getHeaders();
        $headers->addHeaderLine('request_id', uniqid());
        $headers->addHeaderLine('uid', rand(1, 9999));
        $data['data'] = $request . '';
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