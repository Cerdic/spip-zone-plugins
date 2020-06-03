<?php
/**
 * Plugin ckeditor-spip3-plugin
 * (c) 2012 Frédéric Bonnaud
 * Licence GNU/GPL v2
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/*
 * Un fichier d'options permet de definir des elements
 * systematiquement charges à chaque hit sur SPIP.
 *
 * Il vaut donc mieux limiter au maximum son usage
 * tout comme son volume !
 * 
 */

if (! function_exists("json_encode") ) {
	include_spip("inc/json_encode") ;
}

include_spip('inc/filtres') ;

function ckversion($dum) {
	$v=abs(version_svn_courante(dirname(__FILE__)));
	return ($v?$v:'');
}

function ck_split($item) {
	$result = preg_split("~(\||\\|/)~", $item) ;
	return (is_array($result)?$result:array($item, $item)) ;
}

function ck_enliste($texte, $double = false) {
	$texte = trim($texte) ;
	if (!$texte) return array() ;
	$result = preg_split("~\s*[,;:]\s*~", $texte) ;
	if ($double) {
		$result = array_map('ck_split', $result) ;
	}
	return $result ;
}

?>
