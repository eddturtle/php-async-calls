<?php

class Async 
{

	CONST TYPE_RAW = 1;
	CONST TYPE_PHP = 2;


	/**
	 * An array of all the possible options and their respective
	 * default values.
	 *
	 * @var array
	 */
	private $options = [
		'debug' => false,
		'type' => self::TYPE_PHP,
		'async' => true,
		'tmp-dir' => '/tmp/',
		'cleanup' => true
	];

	/**
	 * The queue to store all the commands, as a first-in first-out
	 * scenario. This will get processed be destruction of this class.
	 *
	 * @var array
	 */
	private $fifo = [];

	private $savedFiles = [];


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
		$this->fifo[] = $cmd;
	}

	public function getQueue() {
		return $this->fifo;
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
		if ($this->options['type'] === self::TYPE_PHP) {
			// Old Option: Run direct from terminal (doesn't like brackets too much).
			// $cmd = "php -r \"{$command}\"";

			// New Option: Save to temp file & run it.
			$this->savedFiles[] = $this->options['tmp-dir'] . 'async-tmp-' . time() . '.php';
			$result = file_put_contents(
				end($this->savedFiles),
				$this->_generateCode($command),
				LOCK_EX
			);

			if ($result !== false) {
				$cmd = "php " . end($this->savedFiles);
			}
			else {
				$cmd = "php --help";
			}
		}
		else {
			$cmd = $command;
		}

		if ($this->options['async']) {
			// This forks the process.
			// Helpful guide @ https://segment.com/blog/how-to-make-async-requests-in-php/
			$cmd .= ' > /dev/null 2>&1 &';
		}

		exec($cmd, $output, $exit);

		if ($this->options['debug']) {
			echo '<br />Command Run: ' . $cmd;
		}

		return $exit === 0;
	}


	/**
	 * Generate the content to save to the temporary file. This adds the necessary php tags
	 * and will optionally clean up the temp file afterwards.
	 *
	 * @param string $command the code to execute.
	 *
	 * @return string the full code to save to file.
	 */
	private function _generateCode($command)
	{
		$code = "<?php" . PHP_EOL .
				$command . PHP_EOL;

		if ($this->options['cleanup']) {
			$code .= "unlink('" . end($this->savedFiles) . "');";
		}

		return $code;
	}


}

