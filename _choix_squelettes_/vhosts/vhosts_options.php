<?php

global $dossier_squelettes, $requiredHost, $vhosts;

// par défaut, les squelettes sont a la racine du repertoire squelettes
// attention, cela signifie que le choix des squelettes par vhost ne marche que
// pour ceux presents dans ce repertoires, et pas ceux dans d'eventuels plugins
$requiredHost='';

if (lire_fichier(_DIR_SESSIONS . 'vhosts.txt', $vhosts)) {
	$vhosts = @unserialize($vhosts);
} else {
 	$vhosts = array();
}
error_log("vhosts = ".var_export($vhosts, 1));

if(array_key_exists("HOST", $HTTP_GET_VARS)) {
	// on fixe de force si la valeur est dans l'url
	$requiredHost= $HTTP_GET_VARS["HOST"];
	// on enleve cet argument de l'url, pour pas polluer les liens qui
	// seront generes par la suite
	str_replace("&HOST=$requiredHost", '', $GLOBALS['REQUEST_URI']);
	str_replace("?HOST=$requiredHost", '', $GLOBALS['REQUEST_URI']);

	// slashs interdits => dans ce cas, on ne le met pas dans le cookie
	if (strstr($requiredHost, '/')) {
		$requiredHost='';
	} else {
		setcookie("HOST", $requiredHost);
	}
} elseif(!empty($HTTP_COOKIE_VARS["HOST"])) {
	// sinon, on la cherche dans un cookie
	 $requiredHost= $HTTP_COOKIE_VARS["HOST"];
	 // slashs interdits, meme en tripotant les cookies
	 if (strstr($requiredHost, '/')) {
		 $requiredHost='';
	 }
} else {
	 // sinon, selon le HTTP_HOST
	if(array_key_exists($HTTP_HOST, $vhosts)) {
		$requiredHost=$vhosts[$HTTP_HOST];
	} else {
		$requiredHost='';
	}
}

error_log("requiredHost=$requiredHost");
$dossier_squelettes= "squelettes/$requiredHost";


?>
