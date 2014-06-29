<?php
/**
 * @author daithi-coombes
 */

class CLI{

	/** @var string Hexedecimal rgb string. Default 'ffffff' */
	public $color 		= 'ffffff';
	/** @var string Hexedecimal rgb string. Default 'c0c0c0' */
	public $background 	= 'c0c0c0';
	/** @var integer The width in pixels. Default '960' */
	public $width 		= 960;
	/** @var integer The height in pixels. Default '600' */
	public $height 		= 600;
	/** @var string The text to display. Default 'Image not found' */
	public $text		= 'Image not found';
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
	public function validate_arguments(){

		$this->arguments = $this->get_cli_arguments();

		if( !$this->arguments )
			$this->arguments = $this->get_global_arguments();

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
	private function get_global_arguments(){

		if( !count($this->required) )
			return false;

		foreach( $this->required as $param )
			if( empty($_GLOBALS[$param]) || !$_GLOBALS[$param] )
				return false;
			else
				$args[ $param ] = $_GLOBALS[$param];

		return $args;
	}
}