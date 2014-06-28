<?php
/**
 * Image.class.php
 * Licensed under GNU LGPL
 * @author Daniel Alkemic Czuba <dc@danielczuba.pl>
 * @link http://danielczuba.pl/project/formvalidator/ Project homepage
 * @link http://demo.danielczuba.pl/FormValidator/ Project demos
 */

/**
 * Image( int $x, int $y, str $color )<br>
 * Image( int $x, int $y )<br>
 * Image( str $file_name )<br>
 * This class is a wrapper, around most function that are available in php to
 * manipulate images. I've this becouse I'm tired of all this mess I've made
 * writing script in which I need to manipulate images, and it's really annoying<br>
 * When creating new image $color value can be also 'trans', to create transparent
 * image, if no $color is given, then it's create transparent image as well.
 * @param integer|string
 */
class Image{
	private $im; // this keeps the resource handler
	private $file_name = ''; // file name
	public $width = 0; // width of current image
	public $height = 0; // height of current image
	public $mime_type = NULL; // MIME type of current image
	public $color = 'ffffff'; // curent used color

	public function __construct(){
		$args = func_get_args();
		$num_args = func_num_args();

		// one arguments means, that we are opening a file
		if( $num_args == 1 ){
			$this->file_name = $args[0];
			$this->mime_type = mime_content_type( $this->file_name );
			switch( $this->mime_type ){
				case 'image/png': $this->im = @imagecreatefrompng( $this->file_name ); break;
				case 'image/jpeg': $this->im = @imagecreatefromjpeg( $this->file_name ); break;
				case 'image/gif': $this->im = @imagecreatefromgif( $this->file_name ); break;
				default: throw new Exception('Only PNG, GIF, JPEG.'); break;
			}
		}
		// two arguments (height and width) means we are creating new file
		elseif( $num_args == 2 ){
			$this->im = imageCreateTrueColor( $args[0], $args[1] );
			imagesavealpha( $this->im, true );
			$trans_colour = imageColorallocateAlpha( $this->im, 0, 0, 0, 127 );
			imagefill( $this->im, 0, 0, $trans_colour );
		}
		// two arguments (height, width and color) means we are creating new file with given color
		elseif( $num_args == 3 ){
			$this->im = imageCreateTrueColor( $args[0], $args[1] );
			// transparent
			if( $args[2] == 'trans' ){
				imagesavealpha( $this->im, true );
				$trans_colour = imageColorallocateAlpha( $this->im, 0, 0, 0, 127 );
				imagefill( $this->im, 0, 0, $trans_colour );
			}
			// filing with color
			else{
				$this->setColor( $args[2] );
				imagefill( $this->im, 0, 0, $this->color );
			}
		}
		// something else
		else{
			throw new Exception('Wrong amount of arguments');
		}
		// setting up size
		$this->width = imageSX( $this->im );
		$this->height = imageSY( $this->im );
	}

	public function __destruct(){

		if( $this->im )
			imagedestroy( $this->im );
	}

	/**
	 * Returns image resource handler
	 */
	public function __toString(){
		return $this->im;
	}

	/**
	 * Converts a hex value color into an array
	 * @param string $rgb A hex color
	 * @return array Array with separeted RGB color values in dec
	 */
	public function rgbToArray( $rgb ) {
		return array(
			base_convert(substr($rgb, 0, 2), 16, 10),
			base_convert(substr($rgb, 2, 2), 16, 10),
			base_convert(substr($rgb, 4, 2), 16, 10),
		);
	}

	/**
	 * This function sets up a color
	 * @param string $hex_color A HEX value (ie. ff00ff, 6da8b3) color
	 */
	public function setColor( $hex_color ){
		$rgb_color = $this->rgbToArray( $hex_color );
		$this->color = imageColorAllocate( $this->im, $rgb_color[0], $rgb_color[1], $rgb_color[2] );
	}

	/**
	 * Set a font name to use it in other methods
	 * @param string $name Path to font
	 */
	public function setFont( $name ){
		$this->font_name = $name;
	}

	/**
	 * Adds text, the font name and color must be allready setted
	 * @param integer $size Size of the font
	 * @param integer $x X coordinate of text begining
	 * @param integer $y Y coordinate of text begining
	 * @param integer $angle An angle of tekst
	 * @return array|bool If operation succeed return 8 elements array with coordinates of corners or false on fail
	 */
	public function drawText( $size, $x, $y, $text, $angle = 0 ){
		return imagettftext( $this->im, $size, $angle, $x, $y , $this->color, $this->font_name, $text );
	}

	/**
	 * Adds text with a "border" to image, the font name and color must be allready setted
	 * @param integer $size Size of the font
	 * @param integer $x X coordinate of text begining
	 * @param integer $y Y coordinate of text begining
	 * @param string $border_color Border color
	 * @param integer $angle An angle of tekst
	 * @return array|bool If operation succeed return 8 elements array with coordinates of corners or false on fail
	 */
	public function drawTextWithBorder( $size, $x, $y, $text, $border_color, $angle = 0 ){
		$_tmp = $this->color;
		$this->setColor( $border_color );

		for( $_y = $y-1; $_y <=$y+1; $_y++ ){
			for( $_x = $x-1; $_x <=$x+1; $_x++ ){
				imagettftext( $this->im, $size, $angle, $_x, $_y , $this->color, $this->font_name, $text );
			}
		}
		$this->color = $_tmp;
		return imagettftext( $this->im, $size, $angle, $x, $y , $this->color, $this->font_name, $text );
		// @TODO: rewrite this, to consolide all returns
	}

	/**
	 * Copy image from $im to current
	 * @param resource|Image $im Source image
	 * @param integer $dst_x
	 * @param integer $dst_y
	 * @param integer $src_x
	 * @param integer $src_y
	 * @param integer $src_w
	 * @param integer $src_h
	 */
	public function copy( $im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h ){
		$type_of_im = gettype( $im );

		// if we get a Image class instance
		if( $type_of_im == 'object' ){
			$im = $im->get();
		}

		imagecopy( $this->im, $im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h );
	}

	/**
	 * Draws an ellipse with a centre on (x,y) width given width and height
	 * @param integer $cx X coordinate of the center
	 * @param integer $cy Y coordinate of the center
	 * @param integer $width Width of the ecllipse
	 * @param integer $height Height of the ecllipse
	 * @return bool If the operation succeed
	 */
	public function drawEllipse( $cx, $cy, $width, $height ){
		return imageEllipse( $this->im, $cx, $cy, $width, $height, $this->color );
	}

	/**
	 * Draws an ellipse with a centre on (x,y) width given width and height filled with color
	 * @param integer $cx X coordinate of the center
	 * @param integer $cy Y coordinate of the center
	 * @param integer $width Width of the ecllipse
	 * @param integer $height Height of the ecllipse
	 * @return bool If the operation succeed
	 */
	public function drawFilledEllipse( $cx, $cy, $width, $height ){
		return imageFilledEllipse( $this->im, $cx, $cy, $width, $height, $this->color );
	}

	/**
	 * Draws an rectanlge from point (x1,y1) to (x2,y2)
	 * @param integer $x1 X1 coordinate of left upper corner
	 * @param integer $y1 Y1 coordinate of left upper corner
	 * @param integer $x2 X2 coordinate of right bottom corner
	 * @param integer $y2 Y2 coordinate of right bottom corner
	 * @return bool If the operation succeed
	 */
	public function drawRectangle( $x1, $y1, $x2, $y2 ){
		return imageRectangle( $this->im, $x1, $y1, $x2, $y2, $this->color );
	}

	/**
	 * Draws an rectanlge from point (x1,y1) to (x2,y2) filled with color
	 * @param integer $x1 X1 coordinate of left upper corner
	 * @param integer $y1 Y1 coordinate of left upper corner
	 * @param integer $x2 X2 coordinate of right bottom corner
	 * @param integer $y2 Y2 coordinate of right bottom corner
	 * @return bool If the operation succeed
	 */
	public function drawFilledRectangle( $x1, $y1, $x2, $y2 ){
		return imageFilledRectangle( $this->im, $x1, $y1, $x2, $y2, $this->color );
	}

	/**
	 * Outputs the image to the browser (with proper header), or saves it on a disk.
	 * @param string $type A type i
	 * @param string $name The path to save the file to. If NULL, then image will be outputted to browser.
	 * @param integer $quality A quality of image, for JPEG it's betwen 0-100 (quality level), for PNG 0-9 (compresion level)
	 */
	public function output( $type = 'jpeg', $name = NULL, $quality = 95 ){
		$quality = (int)$quality;
		switch( $type ){
			case 'png':
				$quality = $quality > 9 ? 9 : $quality;
				$quality = $quality < 0 ? 0 : $quality;
				if( !$name ) header( 'Content-type: image/png' );
				return imagePNG( $this->im, $name, $quality );
				break;
			case 'gif':
				if( !$name ) header( 'Content-type: image/gif' );
				return imageGIF( $this->im, $name );
				break;
			case 'jpeg':
			case 'jpg':
			default:
				$quality = $quality > 100 ? 100 : $quality;
				$quality = $quality < 0 ? 0 : $quality;
				if( !$name ) header( 'Content-type: image/jpeg' );
				return imageJpeg( $this->im, $name, $quality );
				break;
		}
	}

	/**
	 * Scales image to given percentage value
	 * @param integer scale
	 */
	public function scale( $scale ){
		$new_width = round( $scale * .01 * $this->width );
		$new_height= round( $scale * .01 * $this->height );

		$new_im = imagecreatetruecolor( $new_width, $new_height );

		imagecopyresampled( $new_im, $this->im, 0, 0, 0, 0, $new_width, $new_height, $this->width, $this->height );

		$this->im = $new_im;
		$this->width = $new_width;
		$this->height = $new_height;
		unset( $new_im );
	}

	/**
	 * Draws a line on given height.
	 * @param integer $y
	 */
	public function drawLineY( $y ){
		imageline( $this->im, 0, $y, $this->width, $y, $this->color );
	}

	/**
	 * Draws a line on given width.
	 * @param integer $x
	 */
	public function drawLineX( $x ){
		imageline( $this->im, $x, 0, $x, $this->height, $this->color );
	}

	/**
	 * Draws a line from point (x1,y1) to (x2,y2)
	 * @param integer $x1
	 * @param integer $y1
	 * @param integer $x2
	 * @param integer $y2
	 */
	public function drawLine( $x1, $y1, $x2, $y2 ){
		imageline( $this->im, $x1, $y1, $x2, $y2, $this->color );
	}

	/**
	 * Resize image to given values
	 * @param integer new_width A new width of image
	 * @param integer new_height A new height of image
	 */
	public function resize( $new_width, $new_height ){
		$new_im = imagecreatetruecolor( $new_width, $new_height );

		imagecopyresampled( $new_im, $this->im, 0, 0, 0, 0, $new_width, $new_height, $this->width, $this->height );

		$this->im = $new_im;
		$this->width = $new_width;
		$this->height = $new_height;
		unset( $new_im );
	}

	public function get(){
		return $this->im;
	}
}