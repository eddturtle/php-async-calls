<?php

class Async 
{
	CONST CALL_RAW = 1;
	CONST CALL_PHP = 2;
	CONST CALL_CURL = 3;


	private $options = [
		'debug' => false,
		'type' => self::CALL_PHP,
		'async' => true
	];

	private $fifo = [];


	public function __construct($options = []) 
	{
		if (!empty($options)) {
			$this->setOptions($options);
		}
	}

	public function __destruct() 
	{
		$this->_processQueue();
	}


	public function setOptions($options) 
	{
		foreach ($this->options as $key => $option) {
			if (isset($options[$key])) {
				$this->options[$key] = $options[$key];
			}	
		}
	}

	public function getOptions()
	{
		return $this->options;
	}


	private function _cleanCommand($cmd) 
	{
		$cmd = $this->_stripSemiColon($cmd);
		return $cmd;
	}

	private function _stripSemiColon($cmd) 
	{
		if (substr($cmd, -1) === ";") {
			$cmd = substr($cmd, 0, -1);
		}
		return $cmd;
	}


	public function queue($cmd) 
	{
		$this->fifo[] = $this->_cleanCommand($cmd);
	}


	private function _processQueue() 
	{
		$results = [];
		foreach ($this->fifo as $command) {
			$results[] = $this->_run($command);
		}
		return count(array_unique($results)) === 1;
	}


	private function _run($command) 
	{
		if ($this->options['type'] === self::CALL_PHP) {
			$cmd = "php -r \"{$command}\"";
		}
		else {
			$cmd = $command;
		}

		if ($this->options['async']) {
			$cmd .= ' > /dev/null 2>&1 &';
		}

		exec($cmd, $output, $exit);

		if ($this->options['debug']) {
			echo '<br />Command Run: ' . $cmd;
			var_dump($output);
			var_dump($exit);
		}
		return $exit === 0;
	}


}

