<?php
/**
 * Plugin Adminer pour Spip
 * Licence GPL 3
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// aiguiller sur adminer si les bonnes conditions
if (strpos($_SERVER['REQUEST_URI'],"prive.php")!==false AND !_DIR_RESTREINT){
	if (
	(($f=_request('file') AND in_array($f,array('default.css','functions.js','favicon.ico')) AND _request('version'))
	OR ($_COOKIE['spip_adminer']))
		AND !_request('page'))
		$GLOBALS['fond'] = 'adminer';
}

function autoriser_adminer_menu_dist($faire,$quoi,$id,$qui,$options){
	return autoriser('webmestre','',$id,$qui,$options);
}