<?php

// create new phpThumb() object
require_once('../phpthumb.class.php');
$phpThumb = new phpThumb();

// set data
$image_filename = '../images/loco.jpg';
$phpThumb->setSourceFilename($image_filename);
// or $phpThumb->setSourceData($binary_image_data);
// or $phpThumb->setSourceImageResource($gd_image_resource);

// set parameters (see "URL Parameters" below)
$phpThumb->w = 100;

// set options (see phpThumb.config.php)
// here you must preface each option with "config_"
$phpThumb->config_output_format = 'gif';

// Set error handling (optional)
$phpThumb->config_error_die_on_error = false;

// generate & output thumbnail
$output_filename = '';
if ($phpThumb->GenerateThumbnail()) {
	if ($output_filename) {
		if (!$phpThumb->RenderToFile($output_filename)) {
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