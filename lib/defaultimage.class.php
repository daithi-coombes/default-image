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

	/* @var integer Flag to disable default image caching */
	const CACHE_OFF = 10;
	/* @var integer Flag to enable default image caching */
	const CACHE_ON = 11;

	/* @var boolean Wether to create color templates for default images */
	public $cache_image = true;

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
		$image = new DefaultImage( $requested_file, $this->_color );

		//create the file
		if( !file_exists($image->filename) && $this->cache_image )
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
	 * Turn image cache on/off
	 * @param boolean $state
	 */
	public function set_image_cache( $state ){

		//set state
		if( $state==self::CACHE_OFF )
			$this->cache_image = false;
		elseif( $state==self::CACHE_ON )
			$this->cache_image = true;

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

	public $color;
	public $info;
	public $filename;

	function __construct( $filename, $color ){

		$this->color 		= strtolower( $this->parse_color( $color ) );
		$this->info 		= pathinfo( $filename );
		$this->filename 	= "{$this->info['dirname']}/"
								. "{$this->info['filename']}"
								. "-{$this->color}"
								. ".{$this->info['extension']}";
	}

	/**
	 * Format the color code
	 * @param  string $color The input color code
	 * @return string        The formated hex code
	 */
	function parse_color( $color ){

		//make sure color is prepended with #
		$color = trim( $color, '#' );
		$color = '#'.$color;

		//get full hex representation
		$rgb = $this->html2rgb( $color );
		$color = $this->rgb2html( $rgb );

		return trim( $color, '#' );
	}

	/**
	 * @link http://www.anyexample.com/programming/php/php_convert_rgb_from_to_html_hex_color.xml
	 * @param  string $color A 3 or 6 digit hex string
	 * @return integer        9 digit rgb integer
	 */
	function html2rgb($color){

		$color = substr($color, 1);

		if (strlen($color) == 6)
			list($r, $g, $b) = array($color[0].$color[1],
				$color[2].$color[3],
				$color[4].$color[5]);
		elseif (strlen($color) == 3)
			list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		else
			return false;

		$r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

		return array($r, $g, $b);
	}

	/**
	 * @link http://www.anyexample.com/programming/php/php_convert_rgb_from_to_html_hex_color.xml
	 * @param  integer  $r Red
	 * @param  integer $g Green
	 * @param  integer $b Blue
	 * @return string     A 6 digit hex string
	 */
	function rgb2html($r, $g=-1, $b=-1){
		if (is_array($r) && sizeof($r) == 3)
		list($r, $g, $b) = $r;

		$r = intval($r); $g = intval($g);
		$b = intval($b);

		$r = dechex($r<0?0:($r>255?255:$r));
		$g = dechex($g<0?0:($g>255?255:$g));
		$b = dechex($b<0?0:($b>255?255:$b));

		$color = (strlen($r) < 2?'0':'').$r;
		$color .= (strlen($g) < 2?'0':'').$g;
		$color .= (strlen($b) < 2?'0':'').$b;
		return '#'.$color;
	}
}