<?php

function minify_compacte($liste_fichiers, $type=NULL){
	$files = array();
	foreach($liste_fichiers as $f) {
		if ($f = find_in_path(trim($f)))
		$files[] = $f;
	}
	define('MINIFY_CACHE_DIR',_DIR_VAR);
	define('MINIFY_ENCODING', $GLOBALS['meta']['charset']);
	define('MINIFY_USE_CACHE', false); // on utilise le cache de spip
	define('MINIFY_BASE_DIR',realPath(_DIR_RACINE));

	include_spip('minify/minify');
	
	// Determine the content type based on the extension of the first file
	// requested.
	if ($type==NULL)
		$type = preg_match('/\.js$/iD', $files[0]) ? Minify::TYPE_JS : Minify::TYPE_CSS;
	elseif($type=='js') $type = Minify::TYPE_JS;
	elseif($type=='css') $type = Minify::TYPE_CSS;

	// Minify and spit out the result.
	try {
		$minify = new Minify($type, $files);
		return $minify->combine();
	}
	catch (MinifyException $e) {
		return "Erreur minify";
	}
	return "Erreur minify";
}

?>
