<?php
/**
 * @author daithi-coombes
 */

class CLI{

	/** @var array Defaults array. An array of required params from CLI or 
		global scope. */
	private $required 	= array();
	/** @var array An array with arguments as params */
	private $arguments 	= array();
	/** @var mixed Error object if error, or NULL if none. Default null */
	private $error 		= null;

	/**
	 * Factory method
	 * @return CLI Returns new cli instance
	 */
	static public function factory(){

		return new CLI();
	}

	/**
	 * Get arguments
	 * @return mixed An array of arguments or Error object if none
	 */
	public function get_arguments(){

		if( is_object($this->error) )
			return $this->error;

		if( count($this->arguments) )
			return $this->arguments;
		else
			return new Error('No arguments passed');
	}

	/**
	 * Set the required arguments from CLI or global scope.
	 * @param array $defaults An array of $param=>$default_value pairs.
	 * @return CLI Returns this instance for chainging.
	 */
	public function set_required( array $required ){

		$this->required = $required;

		return $this;
	}

	/**
	 * Will check cli for arguments, if none will pull from $_REQUEST.
	 * @return CLI Returns this instance for chainging.
	 */
	public function validate_arguments( $global_scope=null ){

		//cli params
		if( empty($global_scope) )
			$this->arguments = $this->get_cli_arguments();

		//global scope params
		else
			$this->arguments = $this->get_global_arguments( $global_scope );

		//error report
		if( !$this->arguments )
			$this->error = new Error('Invalid arguments');

		return $this;
	}

	private function get_cli_arguments(){
		return false;
	}

	/**
	 * Get argumetns from global scope
	 * @return array An array of arg=>value pairs
	 */
	private function get_global_arguments( array $arguments ){

		//error report
		if( !count($this->required) )
			return false;

		$arg_params = array_keys( $arguments );
		if( array_diff( $this->required, $arg_params) )
			return false;

		//set params
		foreach( $this->required as $param )
			$this->$param = $arguments;

		//return
		return $arguments;
	}
}