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
	/* @var string Full path to font file */
	private $_font 		= '';
	private $_height 	= '640';
	private $_text		= null;
	private $_width 	= '960';
	private $error 		= null;
	/* @var DefaultImage A default image instance. @see ::set_image() */
	private $image 		= null;
	private $text 		= '';
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

	/**
	 * Factory method to construct default image.
	 * If image doesn't exist it will be created.
	 * @return DefaultImage Returns a new DefaultImage
	 */
	public function create(){

		if( empty($this->_filename) || !$this->_filename )
			return new Error('Invalid filename');

		$this->worker = self::factory( $this->worker, $this->_width, $this->_height, $this->_color );
		$requested_file = $this->_dir . '/' . $this->_filename;
		$image = new DefaultImage( $requested_file );

		//create the file
		if( !file_exists($image->filename) )
			$this->worker->output( $image->info['extension'], $image->filename );

		return $image;
	}

	/**
	 * Print final image to stdout
	 * @return mixed If error return Error instance, or DefaultImager on
	 * success.
	 */
	public function display(){

		if( $this->error )
			return $this->error;

		$this->worker->displayImage( $this->image->info['extension'] );
	}

	/**
	 * Resize the default image.
	 * $this->image must hold a DefaultImage instance
	 * @return DefaultImager Returns self for chaining.
	 */
	public function resize( $width, $height ){

		if( $this->error )
			return $this;

		$this->worker->cropImage( $width, $height );

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
	 * Set the font
	 * @param string $font The absolute path to the ttf font (websafe fonts
	 * have issues)
	 */
	public function set_font( $font ){

		if( $this->error )
			return $this;

		$this->_font = $font;
		return $this;
	}

	/**
	 * Set the default image.
	 * Constructs worker.
	 * @param DefaultImage $default_image The default image instance created by
	 * buliding an image worker.
	 */
	public function set_image( DefaultImage $default_image ){

		$this->image = $default_image;

		//create worker
		$this->worker = self::factory( $this->worker, $this->image->filename );		

		return $this;
	}

	/**
	 * Set the text.
	 * @param string $text Optional. Set the default image text.
	 */
	public function set_text( $text=null ){

		if( $this->error )
			return $this;

		if( $text )
			$this->worker->addText( $text, '20x20', 0, '#'.$this->_color, 12, 0, $this->_font );

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

/**
 * Image datatype
 */
class DefaultImage{

	public $info;
	public $filename;

	function __construct( $filename ){

		$this->filename 	= $filename;
		$this->info 		= pathinfo( $filename );
	}
}