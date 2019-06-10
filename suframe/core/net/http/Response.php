<?php
/**
 * User: qian
 * Date: 2019/6/10 15:56
 */

namespace suframe\core\net\http;


use suframe\core\traits\Singleton;

class Response {
	use Singleton;

	public function success($respData){
		return $this->write($respData);
	}

	public function error($respData){
		$response = array(
			'HTTP/1.1 500',
		);
		return $this->write($respData, $response);
	}

	public function notFound($respData){
		$response = array(
			'HTTP/1.1 404',
		);
		return $this->write($respData, $response);
	}

	public function write($respData, $response = []){
		//响应行
		$response = array(
			'HTTP/1.1 200',
		);
		//响应头
		$headers = array(
			'Server' => 'SwooleServer',
			'Content-Type' => 'text/html;charset=utf8',
			'Content-Length' => strlen($respData),
		);
		foreach ($headers as $key => $val) {
			$response[] = $key . ':' . $val;
		}
		//空行
		$response[] = '';
		//响应体
		$response[] = $respData;
		$send_data = join("\r\n", $response);
		return $send_data;
	}

}