<?php

/*
 * Sauvegarder la connexion apres le spip_connect_db
 */ 
function mutu_connect_db($host, $port, $login, $pass, $db='', $type='mysql', $prefixe='', $ldap=''){
	$args = func_get_args();
	
	// sinon plantages 1.9.3 
	// le fichier SERVEUR_out dans spip_connect_db
	// empeche une nouvelle connexion ?
	define('_ECRIRE_INSTALL', true);
	
	$link = call_user_func_array('spip_connect_db', $args);
	if($link){
		$GLOBALS['connexions'][_INSTALL_SERVER_DB] = $link;
		$GLOBALS['connexions'][_INSTALL_SERVER_DB][$GLOBALS['spip_sql_version']] 
			= $GLOBALS['spip_' . _INSTALL_SERVER_DB .'_functions_' . $GLOBALS['spip_sql_version']];
	}
	return $link;	
}


// 1 occurrence (lorsque creer_user_base = true)
// utile reellement ?
function mutu_close(){
	$f = _INSTALL_SERVER_DB . '_close';
	$f($GLOBALS['connexions'][_INSTALL_SERVER_DB]['link']);
}



?>
