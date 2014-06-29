<?php
/**
 * Error class
 * 
 * @package defaultImage
 * @author daithi coombes
 */
class Error{

	/* @var string The error message */
	private $message;
	/* @var array The stack trace when Error was constructed */
	private $stacktrace;

	/**
	 * Constructor
	 * @param string $msg The error message
	 */
	function __construct( $msg ){

		$this->message = $msg;
		$this->stacktrace = debug_backtrace();
	}

	/**
	 * Test variable is error or not
	 * @param  mixed  $thing Variable to test
	 * @return boolean
	 */
	static public function is_error( $thing ){

		if( is_object($thing) )
			if( get_class($thing)=='Error' )
				return true;

		return false;
	}

	/**
	 * Get the error message
	 * @return string The error message
	 */
	public function get_message(){

		return $this->message;
	}

	/**
	 * get the stack trace
	 * @return array The result of debug_backtrace()
	 */
	public function get_stacktrace(){

		return $this->stacktrace;
	}

}