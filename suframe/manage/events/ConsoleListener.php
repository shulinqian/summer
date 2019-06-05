<?php
namespace suframe\manage\events;

use suframe\manage\Core;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;

class ConsoleListener implements ListenerAggregateInterface{

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
		return true;
		$this->listeners[] = $events->attach(Events::E_CONSOLE_INIT_BEFORE, [$this, 'initBefore']);
		$this->listeners[] = $events->attach(Events::E_CONSOLE_INIT_AFTER, [$this, 'initAfter']);

		$this->listeners[] = $events->attach(Events::E_CONSOLE_RUN_BEFORE, [$this, 'runBefore']);
		$this->listeners[] = $events->attach(Events::E_CONSOLE_RUN_AFTER, [$this, 'runAfter']);
	}

	public function initBefore(EventInterface $e){

	}
	public function initAfter(EventInterface $e){

	}
	public function runBefore(EventInterface $e){

	}
	public function runAfter(EventInterface $e){

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