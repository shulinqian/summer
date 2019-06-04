<?php
namespace suframe\core\console;

use Inhere\Console\IO\Input;
use Inhere\Console\IO\Output;
use suframe\core\traits\Singleton;

class Console{

	use Singleton;

	protected $app;
	protected $input;
	protected $output;

	/**
	 * @return mixed
	 */
	public function getInput() {
		if(!$this->input){
			$this->input = new Input();
		}
		return $this->input;
	}

	/**
	 * @param mixed $input
	 */
	public function setInput($input): void {
		$this->input = $input;
	}

	/**
	 * @return mixed
	 */
	public function getOutput() {
		if(!$this->input){
			$this->input = new Output();
		}
		return $this->output;
	}

	/**
	 * @param mixed $output
	 */
	public function setOutput($output): void {
		$this->output = $output;
	}

	/**
	 * @return Application
	 */
	public function getApp(array $meta) {
		if(!$this->app){
			$this->app = new Application($meta, $this->getInput(), $this->getOutput());
		}
		return $this->app;
	}

	/**
	 * @param mixed $app
	 */
	public function setApp($app): void {
		$this->app = $app;
	}

}