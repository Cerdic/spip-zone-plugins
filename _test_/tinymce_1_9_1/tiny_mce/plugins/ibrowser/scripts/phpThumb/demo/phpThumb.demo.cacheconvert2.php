<?php
//////////////////////////////////////////////////////////////
///  phpThumb() by James Heinrich <info@silisoftware.com>   //
//        available at http://phpthumb.sourceforge.net     ///
//////////////////////////////////////////////////////////////
///                                                         //
// phpThumb.demo.cacheconvert2.php                          //
// James Heinrich <info@silisoftware.com>                   //
//                                                          //
// phpThumb() cache filename converter v2                   //
// For converting cached filenames from v1.4.6-1.5.3 to     //
// v1.5.4+                                                  //
//                                                          //
//////////////////////////////////////////////////////////////

function RenameFileIfNeccesary($oldfilename) {
	$output  = 'Found: <font color="blue">'.htmlentities($oldfilename, ENT_QUOTES).'</font><br>';
	$oldbasefilename = basename($oldfilename);
	if (eregi('^phpThumb_cache_(.*)_q([0-9]+)_(jpeg|png|gif)$', $oldbasefilename, $matches)) {

		$output .= '<font color="darkgreen">matched filename structure for v1.4.6 - 1.5.3</font><br>';
		list($fullmatch, $baseparameters, $qval, $tformat) = $matches;
		$tformat = strtolower($tformat);
		$cache_filename = 'phpThumb_cache_'.$baseparameters.(($tformat == 'jpeg') ? '_q'.$qval : '').'.'.$tformat;
		$cache_filename = str_replace('%A6', '%7C', $cache_filename);
		$output .= 'attempting to rename to "'.htmlentities($cache_filename, ENT_QUOTES).'"<br>';
		if (file_exists(dirname($oldfilename).'/'.$cache_filename)) {

			$output .= '<font color="red">destination file already exists! cannot rename</font><br><br>';
			echo $output;
			return false;

		} elseif (rename($oldfilename, dirname($oldfilename).'/'.$cache_filename)) {

			$output .= '<font color="green">success!</font><br><br>';
			echo $output;
			return true;

		}
		$output .= '<font color="red">failed to rename! (check permissions?)</font><br><br>';
		echo $output;
		return false;

	} elseif (eregi('^phpThumb_cache_(.*)_([0-9]+)(_q[0-9]+)?\.(jpeg|png|gif)$', $oldbasefilename, $matches)) {

		$output .= '<font color="green">matched filename structure for v1.5.4+ (no need to rename)</font><br>';

	} else {

		$output .= '<font color="orange">did not match any known filename structure (could be from before v1.4.6) - cannot use this file</font><br>';

	}
	$output .= '<font color="orange">not renaming this file</font><br><br>';
	echo $output;
	return true;
}


echo '<html><head><title>phpThumb() cache converter</title></head><body style="font-family: sans-serif; font-size: 9pt;">';
echo '<a href="'.$_SERVER['PHP_SELF'].'">Process another directory</a><hr noshade>';

if (!empty($_POST['cachedir'])) {
	$cachedir = realpath($_POST['cachedir']);
	$skipped = 0;
	if (is_dir($cachedir)) {
		if ($dir = opendir($cachedir)) {
			echo 'Processing directory <b>'.htmlentities($cachedir).'</b><br><br>';
			while ($fileName = readdir($dir)) {
				if (ereg('^phpThumb_cache', $fileName)) {
					RenameFileIfNeccesary($cachedir.'/'.$fileName);
				} elseif (!is_dir($cachedir.'/'.$fileName)) {
					$skipped++;
				}
			}
		} else {
			echo 'Cannot open directory "<b>'.htmlentities($cachedir).'</b>"<br>';
		}
	} else {
		echo '"<b>'.htmlentities($cachedir).'</b>" is not a directory!<br>';
	}
	if ($skipped > 0) {
		echo '<i>skipped '.$skipped.' files</i><br>';
	}
	echo '<hr>';
}

if (!@$_POST['cachedir'] && @include_once('../phpThumb.config.php')) {
	$_POST['cachedir'] = str_replace('\\', '/', $PHPTHUMB_CONFIG['cache_directory']);
}

echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?library='.$_GET['library'].'&lang='.$_GET['lang'].'">';
echo 'Enter the directory you wish to convert from old-style phpThumb() cache filenames to the current naming standard:<br>';
echo '<input type="text" name="cachedir" value="'.@$_POST['cachedir'].'" size="60"><br>';
echo '<input type="submit" value="Convert">';
echo '</form></body></html>';

?>