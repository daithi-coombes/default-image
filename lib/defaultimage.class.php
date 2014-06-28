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

class DefaultImage{

	private $_color		= 'ffffff';
	private $_dir		= 'assets/images';
	private $_ext		= 'png';
	private $_filename	= '';
	private $_height	= 200;
	private $_text		= null;
	private $_width		= 200;

	function __construct(){

	}

	/**
	 * Factory method
	 * @return DefaultImage Returns new instance
	 */
	static public function factory( $type='create' ){

		//get arguments
		$args = func_get_args();
		array_shift($args);

		//get class name
		if( $type=='create' )
			$class = 'Image';

		//construct and return instance
		$obj = new ReflectionClass( $class );
		return $obj->newInstanceArgs( $args );
	}

	public function load_image(){
		
	}

	public function create(){

		//construct image
		parent::__construct( $this->_width, $this->_height, $this->_color );

		//output image
		if( !empty($this->_filename) ){
			$filename = $this->_dir . '/' . $this->_filename . '-' . $this->_color . '.' . $this->_ext;
			$this->output( $this->_ext, $filename );
		}
		else
			$this->output( $this->_ext );
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
}
