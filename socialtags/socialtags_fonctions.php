<?php

//
// ajout feuille de style
//
function socialtags_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('socialtags.css').'" media="all" />'."\n";
	}
	return $flux;
}

//
// ajout cookie + js
//
function socialtags_insert_head($flux){
	$flux = socialtags_insert_head_css($flux); // au cas ou il n'est pas implemente

	// on a besoin de jquery.cookie
	if (!strpos($flux, 'jquery.cookie.js'))
		$flux .= "<script type='text/javascript' src='".find_in_path('javascript/jquery.cookie.js')."'></script>\n";
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