<?php
/**
 * Executable script to create the default image
 *
 * There are no defaults in this script. If script is included on site please 
 * make sure that the following vars are declared first:
 *  - $width
 *  - $height
 *  - $color
 *  - $text
 *  - $filename
 *  - $dir
 *  - $background
 *  - $font (absolute url to ttf font, websafe fonts won't work)
 *  - $fontsize
 *
 * also you need to define the constant BASE_DIR
 * 
 * @author daithi coombes
 */
define( 'BASE_DIR', dirname(dirname(__FILE__)) );

require_once( BASE_DIR . '/lib/image.class.php');
require_once( BASE_DIR . '/lib/defaultimage.class.php');
require_once( BASE_DIR . '/lib/php_image_magician.php');

/**
 * @todo get vars from cli arguments if available
 */

//cli parse arguments

//validate arguments

//load default image
	//create new image?
	//get image

//format(width, height, text)
//display


//get arguments from CLI or global scope
$arugments = CLI::factory()
	->validate_arguments()
	->get_arguments();

//error check $arguments
if( getclass($arguments)=='Error' )
	die( 'Error getting paramaters: '.$arguments->get_message() );

//get/set the default image
$default_image = DefaultImage::factory('create')
	->set_width( $arguments->width )
	->set_height( $arguments->height )
	->set_color( $arguments->color );

//resize and format image
DefaultImage::factory('format')
	->set_image( $default_image )
	->set_text( $arguments->text )
	->resize()
	->display();

//if default color image doesn't exist then create it
$info = pathinfo($filename);
$filename = $dir.'/'.$info['filename'].'-'.$background.'.'.$info['extension'];
if( !file_exists( $filename ) ){

	$create = DefaultImage::factory('create', $width, $height, $background);
	$create->output( $info['extension'], $filename );
}


$image = new imageLib('assets/images/default.png');

$image->addText( $text, '20x20', 0, '#'.$color, 12, 0, $font );
$image->cropImage( $width, $height );
$image->displayImage();