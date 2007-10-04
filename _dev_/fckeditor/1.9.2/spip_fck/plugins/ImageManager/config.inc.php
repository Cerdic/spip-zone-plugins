<?php
/**
 * Image Manager configuration file.
 * @author Wei Zhuo
 * @author Paul Moers <mail@saulmade.nl> - watermarking and replace code + several small enhancements <http://fckplugins.saulmade.nl>
 * @version $Id: config.inc.php,v 1.5 2007/03/17 21:22:13 thierrybo Exp $
 * @package ImageManager
 */


/* 
 File system path to the directory you want to manage the images
 for multiple user systems, set it dynamically.

 NOTE: This directory requires write access by PHP. That is, 
       PHP must be able to create files in this directory.
	   Able to create directories is nice, but not necessary.
*/
//+++ Modif F. SAURET +++++++++++++++++++++++++++++++++++++++++++++++++++++++
//$IMConfig['base_dir'] = '/var/www/FCKeditor/images/';
$dir_relatif_array = split('/', $_SERVER["PHP_SELF"]);
  $i = 0;
  $cheminSpip="";
  while($dir_relatif_array[$i] != 'spip_fck') 
    {
        $i++;
    }
   for ($c=0;$c<$i-2;$c++)
   {
       $cheminSpip .= $dir_relatif_array[$c]."/";
   } 
$IMConfig['base_dir'] = $_SERVER["DOCUMENT_ROOT"].$cheminSpip.'IMG';

//--- Fin modif --------------------------------------------------------------

/*
 The URL to the above path, the web browser needs to be able to see it.
 It can be protected via .htaccess on apache or directory permissions on IIS,
 check you web server documentation for futher information on directory protection
 If this directory needs to be publicly accessiable, remove scripting capabilities
 for this directory (i.e. disable PHP, Perl, CGI). We only want to store assets
 in this directory and its subdirectories.
*/
//+++ Modif F. SAURET +++++++++++++++++++++++++++++++++++++++++++++++++++++++
//$IMConfig['base_url'] = 'http://www.saulmade.nl/FCKeditor/images/';
$IMConfig['base_url'] = 'http://'.$_SERVER['REMOTE_ADDR'].$cheminSpip. 'IMG';
//--- Fin modif --------------------------------------------------------------

$IMConfig['server_name'] = $_SERVER['SERVER_NAME'];

/*
 demo - when true, no saving is allowed
*/
//+++ Modif T.Bothorel+++++++++++++++++++++++++++++++++++++++++++++++++++++++
//$IMConfig['demo'] = true;
$IMConfig['demo'] = false;
//--- Fin modif --------------------------------------------------------------


/*

  Possible values: true, false

  TRUE - If PHP on the web server is in safe mode, set this to true.
         SAFE MODE restrictions: directory creation will not be possible,
		 only the GD library can be used, other libraries require
		 Safe Mode to be off.

  FALSE - Set to false if PHP on the web server is not in safe mode.
*/
$IMConfig['safe_mode'] = false;
//+++ Modif T.Bothorel+++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// Ajout dÃ©tection automatique du safe_mode
if (ini_get ('safe_mode')) {
    $IMConfig['safe_mode'] = true;
}
//--- Fin modif ---------------------------------------------------------------

/* 
 Possible values: 'GD', 'IM', or 'NetPBM'

 The image manipulation library to use, either GD or ImageMagick or NetPBM.
 If you have safe mode ON, or don't have the binaries to other packages, 
 your choice is 'GD' only. Other packages require Safe Mode to be off.
*/
//+++ Modif F. SAURET+++++++++++++++++++++++++++++++++++++++++++++++++++++++++
define('IMAGE_CLASS', 'GD');
// Changer ici pour le type de vignettes voir si récupérable par spip
//--- Fin modif ---------------------------------------------------------------


/*
 After defining which library to use, if it is NetPBM or IM, you need to
 specify where the binary for the selected library are. And of course
 your server and PHP must be able to execute them (i.e. safe mode is OFF).
 GD does not require the following definition.
*/
//+++ Modif F. SAURET+++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//define('IMAGE_TRANSFORM_LIB_PATH', '/ext/imagemagick/bin/');

//--- Fin modif ---------------------------------------------------------------


/* ==============  OPTIONAL SETTINGS ============== */


/*
  The prefix for thumbnail files, something like .thumb will do. The
  thumbnails files will be named as "prefix_imagefile.ext", that is,
  prefix + orginal filename.
*/
$IMConfig['thumbnail_prefix'] = '.';

/*
  Thumbnail can also be stored in a directory, this directory
  will be created by PHP. If PHP is in safe mode, this parameter
  is ignored, you can not create directories. 

  If you do not want to store thumbnails in a directory, set this
  to false or empty string '';
*/
$IMConfig['thumbnail_dir'] = '.thumbs';

/*
  Possible values: true, false

 TRUE -  Allow the user to create new sub-directories in the
         $IMConfig['base_dir'].

 FALSE - No directory creation.

 NOTE: If $IMConfig['safe_mode'] = true, this parameter
       is ignored, you can not create directories
*/

$IMConfig['allow_new_dir'] = false;


/*
  Possible values: true, false

  TRUE - Allow the user to upload files.

  FALSE - No uploading allowed.
*/
$IMConfig['allow_upload'] = true;

/*
  Possible values: true, false

  TRUE - Allow the replacement of the image with a newly uploaded image in the editor dialog.

  FALSE - No replacing allowed.
*/
$IMConfig['allow_replace'] = false;


/*
  Possible values: true, false

  TRUE - Allow the deletion of images

  FALSE - No deleting allowed
*/
$IMConfig['allow_delete'] = false;

/*
  Possible values: true, false

  TRUE - Allow the user to enter a new filename for saving the edited image.

  FALSE - Overwrite
*/
$IMConfig['allow_newFileName'] = true;

/*
  Possible values: true, false
  Only applies when the the user can enter a new filename (The baove settig = 'allow_newFileName' true)

  TRUE - Overwrite file of entered filename, if file already exist.

  FALSE - Save to variant of entered filename, if file already exist.
*/
$IMConfig['allow_overwrite'] = false;

/*
  Specify the paths of the watermarks to use (relative to $IMConfig['base_dir']).
  Specifying none will hide watermarking functionality.
*/
$IMConfig['watermarks'] = array	("imageManager.png");

/*
	To limit the width and height for uploaded files, specify the maximum pixeldimensions.
	Setting either zero or empty will allow any size.
*/
//+++ Modif T.Bothorel+++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// Limitation n'a pas de sens car l'upload par FCKeditor n'en tient pas compte

//$IMConfig['maxWidth'] = 333;
//$IMConfig['maxHeight'] = 333;
$IMConfig['maxWidth'] = 0;
$IMConfig['maxHeight'] = 0;
//--- Fin modif ---------------------------------------------------------------

/*
 Possible values: true, false

 TRUE - If set to true, uploaded files will be validated based on the 
        function getImageSize, if we can get the image dimensions then 
        I guess this should be a valid image. Otherwise the file will be rejected.

 FALSE - All uploaded files will be processed.

 NOTE: If uploading is not allowed, this parameter is ignored.
*/
//+++ Modif T.Bothorel+++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// de nombreuse images valides n'ont pas de thumbnail créé si true

//$IMConfig['validate_images'] = true;
$IMConfig['validate_images'] = false;
//--- Fin modif ---------------------------------------------------------------

/*
 The default thumbnail if the thumbnails can not be created, either
 due to error or bad image file.
*/
$IMConfig['default_thumbnail'] = 'img/default.gif';

/*
  Thumbnail dimensions.
*/
$IMConfig['thumbnail_width'] = 96;
$IMConfig['thumbnail_height'] = 96;

/*
  Image Editor temporary filename prefix.
*/
$IMConfig['tmp_prefix'] = '.editor_';
?>
