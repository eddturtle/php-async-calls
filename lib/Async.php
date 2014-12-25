<?php

class Async 
{

	CONST CALL_RAW = 1;
	CONST CALL_PHP = 2;


	/**
	 * An array of all the possible options and their respective
	 * default values.
	 *
	 * @var array
	 */
	private $options = [
		'debug' => false,
		'type' => self::CALL_PHP,
		'async' => true
	];

	/**
	 * The queue to store all the commands, as a first-in first-out
	 * scenario. This will get processed be destruction of this class.
	 *
	 * @var array
	 */
	private $fifo = [];


	/**
	 * Allow the setting of any options on creation of the object.
	 *
	 * @param array $options any options to over write (doesn't have to match the whole array).
	 */
	public function __construct($options = []) 
	{
		if (!empty($options)) {
			$this->setOptions($options);
		}
	}

	/**
	 * On destroying the object, process the queue first.
	 */
	public function __destruct() 
	{
		$this->_processQueue();
	}


	/**
	 * Set any options, if the inputted, over write the defaults.
	 *
	 * @param array $options any options to over write.
	 */
	public function setOptions($options) 
	{
		foreach ($this->options as $key => $option) {
			if (isset($options[$key])) {
				$this->options[$key] = $options[$key];
			}	
		}
	}

	/**
	 * Get all the current options, if any have been overwritten, they'll appear
	 * here. Useful for checking the state of the object and testing it.
	 *
	 * @return array all the options & values.
	 */
	public function getOptions()
	{
		return $this->options;
	}


	/**
	 * Add a new command to the queue to process. This function will also 'clean'
	 * the command, removing any trailing semi-colons (as they prevent async).
	 *
	 * @param string $cmd the command to queue and run.
	 */
	public function queue($cmd) 
	{
		$this->fifo[] = $this->_cleanCommand($cmd);
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


	/**
	 * Process the queue, stepping through each item in the queue and
	 * running each independently.
	 *
	 * @return bool will return true if more than one process is run and they all return status 0.
	 */
	private function _processQueue() 
	{
		$results = [];
		foreach ($this->fifo as $command) {
			$results[] = $this->_run($command);
		}
		return isset($results[0]) && $results[0] === 0 && count(array_unique($results)) === 1;
	}


	/**
	 * Actually run the command, by detecting the async type and using exec to run it.
	 * Will echo data depending of whether the debug flag is on.
	 *
	 * @param string $command the command to run.
	 *
	 * @return bool whether the exit status is zero.
	 */
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
		}
		return $exit === 0;
	}


}

