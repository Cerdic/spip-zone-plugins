<?php

function googleplus1_css(){
	return '<link rel="stylesheet" type="text/css" href="'.find_in_path('googleplus1.css').'" media="all" />'."\n";
}

/**
 * ajout feuille de style dans le HEAD_CSS
 * pris en charge correctement a partir de SPIP 3
 * @param string $flux
 * @return string
 */
function googleplus1_insert_head_css($flux){
	if (intval($GLOBALS['spip_version_branche'])>=3)
		$flux .= googleplus1_css();
	return $flux;
}

function googleplus1_insert_head($flux){
	if (intval($GLOBALS['spip_version_branche'])<3)
	$flux .= googleplus1_css();

	include_spip('inc/config');
	$googleplus1_lang = lire_config('langue_site');
	$flux .= '
		<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
		{lang: \''.$googleplus1_lang.'\'}
		</script>';
	include_spip('inc/filtres');
	if (function_exists('produire_fond_statique'))
		$jsFile = produire_fond_statique('googleplus1.js');
	else
		$jsFile = generer_url_public('googleplus1.js');

	$flux .= "<script src='$jsFile' type='text/javascript'></script>\n";
	return $flux;
	return $flux;
}
?>
