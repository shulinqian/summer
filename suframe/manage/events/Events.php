<?php
namespace suframe\manage\events;

class Events {

	const E_CONSOLE_INIT_BEFORE = 'console.init.before';
	const E_CONSOLE_INIT_AFTER = 'console.init.after';

	const E_CONSOLE_RUN_BEFORE = 'console.run.before';
	const E_CONSOLE_RUN_AFTER = 'console.run.after';

	const E_TCP_RUN_BEFORE = 'tcp.run.before';
	const E_TCP_RUN_AFTER = 'tcp.run.after';

	const E_TCP_REQUEST = 'tcp.request';
	const E_TCP_RESPONSE_BEFORE = 'tcp.response.before';
	const E_TCP_RESPONSE_AFTER = 'tcp.response.after';

}