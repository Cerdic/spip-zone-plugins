<?php

// recuperere le chemin d'une css existante et :
// cree (ou recree) dans _DIR_VAR/cache_css/ une css dont les url relatives sont passees en url absolues
// http://doc.spip.org/@url_absolue_css
function url_absolue_css ($css) {
	if (!preg_match(',\.css$,i', $css, $r)) return $css;

	$url_absolue_css = url_absolue($css);

	$f = basename($css,'.css');
	$f = sous_repertoire (_DIR_VAR, 'cache-css') 
		. preg_replace(",(.*?)(_rtl|_ltr)?$,","\\1-urlabs-" . substr(md5("$css-urlabs"), 0,4) . "\\2",$f) 
		. '.css';

	if ((@filemtime($f) > @filemtime($css))
	AND ($GLOBALS['var_mode'] != 'recalcul'))
		return $f;

	if ($url_absolue_css==$css){
		if (strncmp($GLOBALS['meta']['adresse_site'],$css,$l=strlen($GLOBALS['meta']['adresse_site']))!=0
		 OR !lire_fichier(_DIR_RACINE . substr($css,$l), $contenu)){
		 		include_spip('inc/distant');
		 		if (!$contenu = recuperer_page($css))
					return $css;
		}
	}
	elseif (!lire_fichier($css, $contenu))
		return $css;

	// passer les url relatives a la css d'origine en url absolues
	$contenu = urls_absolues_css($contenu, $css);

	// ecrire la css
	if (!ecrire_fichier($f, $contenu))
		return $css;

	return $f;
}

// Si la source est un chemin, on retourne un chemin avec le contenu compacte
// dans _DIR_VAR/cache_$format/
// Si c'est un flux on le renvoit compacte
// Si on ne sait pas compacter, on renvoie ce qu'on a recu
// http://doc.spip.org/@compacte
function compacte($source, $format = null) {
	if (!$format AND preg_match(',\.(js|css)$,', $source, $r))
		$format = $r[1];
	if (!function_exists($compacte = 'compacte_'.$format))
		return $source;

	// Si on n'importe pas, est-ce un fichier ?
	if (!preg_match(',[\s{}],', $source)
	AND preg_match(',\.'.$format.'$,i', $source, $r)
	AND file_exists($source)) {
		// si c'est une css, il faut reecrire les url en absolu
  	if ($type=='css')
  		$source = url_absolue_css($source);
		
		$f = basename($source,'.'.$format);
		$f = sous_repertoire (_DIR_VAR, 'cache-'.$format) 
		. preg_replace(",(.*?)(_rtl|_ltr)?$,","\\1-compacte-"
		. substr(md5("$source-compacte"), 0,4) . "\\2", $f, 1)
		. '.' . $format;

		if ((@filemtime($f) > @filemtime($source))
		AND ($GLOBALS['var_mode'] != 'recalcul'))
			return $f;

		if (!lire_fichier($source, $contenu))
			return $source;

		// traiter le contenu
		$contenu = $compacte($contenu);

		// ecrire le fichier destination, en cas d'echec renvoyer la source
		if (ecrire_fichier($f, $contenu))
			return $f;
		else
			return $source;
	}

	// Sinon simple compactage de contenu
	return $compacte($source);
}

// Cette fonction verifie les reglages du site et traite le compactage
// des css et/ou js d'un <head>
// un fichier .gz est cree pour chaque, qui peut etre utilise par apache
// et lui eviter de recompresser a chaque hit, avec les directives suivantes :
//<IfModule mod_gzip.c>
//mod_gzip_on                   Yes
//mod_gzip_can_negotiate        Yes
//mod_gzip_static_suffix        .gz
//AddEncoding              gzip .gz
//mod_gzip_item_include         file       \.(js|css)$
//</IfModule>
// http://doc.spip.org/@compacte_head
function compacte_head($flux){
	include_spip('inc/compresseur');
	// dans l'espace prive on compacte toujours, c'est concu pour
	if ($GLOBALS['meta']['auto_compress_css'] == 'oui' OR (test_espace_prive() AND !defined('_INTERDIRE_COMPACTE_HEAD_ECRIRE')))
		$flux = compacte_head_css($flux);
	if ($GLOBALS['meta']['auto_compress_js'] == 'oui' OR (test_espace_prive() AND !defined('_INTERDIRE_COMPACTE_HEAD_ECRIRE')))
		$flux = compacte_head_js($flux);
	return $flux;
}

?>