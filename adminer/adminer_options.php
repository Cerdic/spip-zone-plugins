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
	((in_array(_request('file'),array('default.css','functions.js','favicon.ico'))
		AND _request('version'))
	OR ($_COOKIE['spip_adminer']))
		AND !_request('page'))
		$GLOBALS['fond'] = 'adminer';
}
