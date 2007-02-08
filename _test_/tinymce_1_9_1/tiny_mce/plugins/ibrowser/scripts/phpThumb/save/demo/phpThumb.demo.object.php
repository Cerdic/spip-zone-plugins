<?php
//////////////////////////////////////////////////////////////
///  phpThumb() by James Heinrich <info@silisoftware.com>   //
//        available at http://phpthumb.sourceforge.net     ///
//////////////////////////////////////////////////////////////
///                                                         //
// phpThumb.demo.object.php                                 //
// James Heinrich <info@silisoftware.com>                   //
//                                                          //
// Example of how to use phpthumb.class.php as an object    //
//                                                          //
//////////////////////////////////////////////////////////////

// create new phpThumb() object
require_once('../phpthumb.class.php');
$phpThumb = new phpThumb();

// set data
$image_filename = '../images/loco.jpg';
$phpThumb->setSourceFilename($image_filename);
// or $phpThumb->setSourceData($binary_image_data);
// or $phpThumb->setSourceImageResource($gd_image_resource);

// set parameters (see "URL Parameters" in phpthumb.readme.txt)
$phpThumb->w = 100;
//$phpThumb->h = 100;
//$phpThumb->fltr[] = 'gam|1.2';

// set options (see phpThumb.config.php)
// here you must preface each option with "config_"
$phpThumb->config_output_format    = 'png';
$phpThumb->config_imagemagick_path = '/usr/local/bin/convert';
//$phpThumb->config_allow_src_above_docroot = true; // needed if you're working outside DOCUMENT_ROOT, in a temp dir for example

// generate & output thumbnail
$output_filename = 'outfile.gif';
if ($phpThumb->GenerateThumbnail()) { // this line is VERY important, do not remove it!
	if ($output_filename) {
		if ($phpThumb->RenderToFile($output_filename)) {
			// do something on success
			echo 'Successfully rendered:<br><img src="'.$output_filename.'">';
		} else {
			// do something with debug/error messages
			die('Failed:<pre>'.implode("\n\n", $phpThumb->debugmessages).'</pre>');
		}
	} else {
		$phpThumb->OutputThumbnail();
	}
} else {
	// do something with debug/error messages
	die('Failed:<pre>'.implode("\n\n", $phpThumb->debugmessages).'</pre>');
}

?>