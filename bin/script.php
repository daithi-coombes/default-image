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

require_once( BASE_DIR . '/lib/error.class.php');
require_once( BASE_DIR . '/lib/cli.class.php');
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

global $defaultimage_params;

//get arguments from CLI or global scope
$arguments = CLI::factory()
	->set_required(array(
		'color',
		'width',
		'height',
		'text',
		'background',
		'filename',
		'font',
		'dir'
	))
	->validate_arguments( $defaultimage_params )
	->get_arguments();

//error check $arguments
if( Error::is_error($arguments) )
	die( 'Error getting paramaters: '.$arguments->get_message() );

//get/create the default image
$default_image = DefaultImager::factory()
	->set_worker( 'Image' )
	->set(array(
		'width' 	=> $arguments['width'],
		'height' 	=> $arguments['height'],
		'color' 	=> $arguments['color'],
		'filename'  => $arguments['filename']
	))
	->create();

var_dump($default_image);
//error check $arguments
if( getclass($default_image)=='Error' )
	die( 'Error creating default image: '.$default_image->get_message() );

//resize and format image
DefaultImager::factory()
	->set_worker('imageLib')
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