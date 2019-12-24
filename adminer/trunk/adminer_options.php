<?php
/**
 * Plugin Adminer pour Spip
 * Licence GPL 3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Commentez cette ligne si vous voulez donner un acces eventuel aux autres bases masquees
// cette option est non fonctionnelle en SQLite, on la desactive par defaut
#define('_ADMINER_VERROUILLER_DB',true);

// aiguiller sur adminer si les bonnes conditions
if (isset($_SERVER['REQUEST_URI']) AND strpos($_SERVER['REQUEST_URI'],"prive.php")!==false AND !_DIR_RESTREINT){
	if (
	(  ($f=_request('file') AND in_array($f,array('default.css','functions.js','favicon.ico','jush.js')) AND _request('version'))
	  OR (isset($_COOKIE['spip_adminer']) AND $_COOKIE['spip_adminer'])
	)
	AND (
		!_request('page')
		  OR ((_request('username') OR _request('db')) AND (_request('server') OR _request('sqlite') OR _request('sqlite2')))
	)
	)
		$GLOBALS['fond'] = 'adminer';
}

function autoriser_adminer_menu_dist($faire,$quoi,$id,$qui,$options){
	return autoriser('webmestre','',$id,$qui,$options);
}
