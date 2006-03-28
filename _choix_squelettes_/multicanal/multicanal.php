<?php

global $dossier_squelettes, $look;

// par défaut, les squelettes sont dans un sous repertoires /html
$dossier_squelettes= 'squelettes/html';

// En attendent un jeu de test plus definitif
if(!empty($HTTP_GET_VARS["LOOK"])) {
	// on fixe de force si la valeur est dans l'url
	$look= $HTTP_GET_VARS["LOOK"];
	setcookie("LOOK", $HTTP_GET_VARS["LOOK"]);
} else {
	if(!empty($HTTP_COOKIE_VARS["LOOK"])) {
		// sinon, on la cherche dans un cookie
		 $look= $HTTP_COOKIE_VARS["LOOK"];
	} else {
		 // sinon, selon le user-agent
		// A REVOIR : gerer a part une liste de ug/look ?
		if( stristr($HTTP_USER_AGENT, "nokia")
		|| stristr($HTTP_USER_AGENT, "OPWV-SDK")) {
			$look= "wap";
		} elseif( stristr($HTTP_USER_AGENT, "portalmmm")) {
			$look= "imode";
		} else {
			$look= "html";
		}
	}
	// on truande la request_uri pour que nom_fichier_cache
	// genere une version par canal => pas de conflit dans le cache
	$GLOBALS['REQUEST_URI'].="&LOOK=$look";
}

$dossier_squelettes= "squelettes/$look";

?>
