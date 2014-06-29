<?php
/**
 * Class for creating a blank image.
 *
 * Used for creating default images. Create a script to handle dimensions and
 * params.
 *
 * @uses Image Uses the lib/image.class.php for creating new images
 * @uses imageLib Uses lib/php_image_magician.php for add text to the image
 * @author daithi commbes
 */

require_once('image.class.php');
require_once('php_image_magician.php');

class DefaultImager{

	private $_color		= 'ffffff';
	private $_dir		= 'assets/images';
	private $_ext		= 'png';
	private $_filename	= '';
	private $_height	= 200;
	private $_text		= null;
	private $_width		= 200;
	private $error 		= null;
	private $worker		= '';

	function __construct(){

	}

	/**
	 * Factory method
	 * @param  string $worker Default null. Class name to build
	 * @return mixed         Default DefaultImage or worker instance if passed
	 */
	static public function factory( $worker=null ){

		//construct worker
		if( $worker ){
			$args = func_get_args();
			array_shift( $args );

			$obj = new ReflectionClass( $worker );
			return $obj->newInstanceArgs( $args );
		}

		//default DefaultImager
		$ret = new DefaultImager();

		return $ret;
	}

	public function create(){

		$this->worker = self::factory( $this->worker, $this->_width, $this->_height, $this->_color );

		//output image
		if( !empty($this->_filename) ){
			$filename = $this->_dir . '/' . $this->_filename . '-' . $this->_color . '.' . $this->_ext;
			$this->worker->output( $this->_ext, $filename );
		}
		else
			$this->worker->output( $this->_ext );

		return $this;
	}

	/**
	 * Set class parameters
	 * @param array $params An associative array of param value pairs
	 */
	public function set( array $params ){

		foreach( $params as $param=>$val ){
			$param = '_'.$param;
			$this->$param = $val;
		}

		return $this;
	}

	/**
	 * Set worker class name
	 * @param string $class_name The worker class name
	 */
	public function set_worker( $class_name ){

		$this->worker = $class_name;

		return $this;
	}
}