<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function socialtags_css(){
	return '<link rel="stylesheet" type="text/css" href="'.find_in_path('socialtags.css').'" media="all" />'."\n";
}

/**
 * ajout feuille de style dans le HEAD_CSS
 * pris en charge correctement a partir de SPIP 3
 * @param string $flux
 * @return string
 */
function socialtags_insert_head_css($flux){
	if (intval($GLOBALS['spip_version_branche'])>=3)
		$flux .= socialtags_css();
	return $flux;
}

/**
 * ajout cookie + js
 * @param  $flux
 * @return string
 */
function socialtags_insert_head($flux){
	if (intval($GLOBALS['spip_version_branche'])<3)
		$flux .= socialtags_css();

	// on a besoin de jquery.cookie
	if (!strpos($flux, 'jquery.cookie.js'))
		$flux .= "<script type='text/javascript' src='".find_in_path('javascript/jquery.cookie.js')."'></script>\n";

	include_spip('inc/filtres');
	if (function_exists('produire_fond_statique'))
		$jsFile = produire_fond_statique('socialtags.js');
	else
		$jsFile = generer_url_public('socialtags.js');

	$flux .= "<script src='$jsFile' type='text/javascript'></script>\n";
	return $flux;
}


// La liste est stockee en format RSS
function socialtags_liste() {
	include_spip('inc/syndic');
	lire_fichier(find_in_path('socialtags.xml'), $rss);
	return analyser_backend($rss);
}

?>
