<?php
namespace suframe\manage\events;

class Events {

	const E_CONSOLE_INIT_BEFORE = 'console.init.before';
	const E_CONSOLE_INIT_AFTER = 'console.init.after';

	const E_CONSOLE_RUN_BEFORE = 'console.run.before';
	const E_CONSOLE_RUN_AFTER = 'console.run.after';

	const E_HTTP_RUN_BEFORE = 'http.run.before';
	const E_HTTP_RUN_AFTER = 'http.run.after';

	const E_HTTP_REQUEST = 'http.request';
	const E_HTTP_RESPONSE_BEFORE = 'http.response.before';
	const E_HTTP_RESPONSE_AFTER = 'http.response.after';

}