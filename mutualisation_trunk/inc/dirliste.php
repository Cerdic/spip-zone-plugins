<?php

// Ce programme appel� en ajax renvoie une  chaine de la forme XXX##rep1##rep2...
//	    o� XXX vaut -1 si erreur et sinon le poids des fichiers du r�pertoire (hors sous-r�pertoires) en Mo
//	    o� rep1, rep2,... le nom des sous-r�pertoires
// Param dir : r�pertoire � explorer

echo dirliste($_GET['dir']);

function dirliste($path)
{
	$liste = "" ;
	$size = 0 ;

	// Trailing slash
	if (substr($path, -1, 1) !== DIRECTORY_SEPARATOR) {
	   $path .= DIRECTORY_SEPARATOR;
	}

   if (!is_dir($path)) {
		return -1;
	}

	$handle=opendir($path) ;
	while (($file = readdir($handle)) !== false) {
		// Skip pointers
		if ($file == '.' || $file == '..') {
			continue;
		}

		if (is_dir($path.$file)) {
			$liste .= '##'.$file;
		} elseif (is_file($path.$file)) {
			$size += filesize($path.$file);
		} else {
			echo $file;
		}
	}
	closedir($handle) ;

	return round($size/1024/1024,2).$liste;

}

?>