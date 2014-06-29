<?php
/**
 * Package for handling missing images.
 * 
 * @usage
 * Move the .htaccess file to the images folder. Set the location of this
 * folder to redirect unkown images
 *
 * eg:
 * ErrorDocument 404 /cms/modules/default_images/index.php
 *
 * @author daithi coombes
 */

ini_set('display_errors','on');
error_reporting( E_ALL );


//if request uri is image (png, jpeg, jpg, gif) then handling a 404, parse vars 
//from $_SERVER['REQUEST_URI']
if( $_SERVER['REDIRECT_STATUS']=='404' ){
	$image_extensions = array( 'jpg','gif','jpeg','png' );
	$request_info = pathinfo( $_SERVER['REQUEST_URI'] );

	preg_match('/([0-9]+)x([0-9]+)$/', $request_info['filename'], $matches);
	$_GET['width'] = $matches[1];
	$_GET['height'] = $matches[2];
}

//static vars
global $font, $width, $height, $filename, $dir;
$font 		= dirname(__FILE__) . '/assets/fonts/OpenSans.ttf';
$width 		= $_GET['width'];
$height 	= $_GET['height'];
$filename 	= 'default.png';
$dir 		= 'assets/images';

//defaults
( @$_GET['background'] ) 	? $background=$_GET['background'] 	: $background='C0C0C0';
( @$_GET['color'] ) 		? $color=$_GET['color'] 			: $color='fff';
( @$_GET['text'] ) 			? $text=$_GET['text'] 				: $text='Image not found';

header('HTTP/1.1 200 OK');

//load script
$defaultimage_params = array(
	'width' 		=> $width,
	'height' 		=> $height,
	'filename' 		=> $filename,
	'dir' 			=> $dir,
	'font' 			=> $font,
	'background' 	=> $background,
	'color' 		=> $color,
	'text' 			=> $text
);
require_once( dirname(__FILE__) . '/bin/script.php');
