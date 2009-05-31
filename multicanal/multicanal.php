<?php

global $dossier_squelettes, $look;

// par défaut, les squelettes sont dans un sous repertoires /html
$dossier_squelettes= 'squelettes/html';

// En attendent un jeu de test plus definitif
if(!empty($HTTP_GET_VARS["LOOK"])) {
	// on fixe de force si la valeur est dans l'url
	$look= $HTTP_GET_VARS["LOOK"];
	// slashs interdits => dans ce cas, on ne le met pas dans le cookie
	if (strstr($look, '/')) {
		$look='html';
	} else {
		setcookie("LOOK", $HTTP_GET_VARS["LOOK"]);
	}
	// on enleve cet argument de l'url, pour pas polluer les liens qui
	// seront genere par la suite
	str_replace("&LOOK=$look", '', $GLOBALS['REQUEST_URI']);
} else {
	if(!empty($HTTP_COOKIE_VARS["LOOK"])) {
		// sinon, on la cherche dans un cookie
		 $look= $HTTP_COOKIE_VARS["LOOK"];
		 // slashs interdits, meme en tripotant les cookies
		 if (strstr($look, '/')) {
			 $look='html';
		 }
	} else {
		 // sinon, selon le user-agent
		// A REVOIR : gerer a part une liste de ug/look ?
		if( stristr($HTTP_USER_AGENT, "nokia")
			|| stristr($HTTP_USER_AGENT, "OPWV-SDK")) {
			$look= "wap";
		} elseif( stristr($HTTP_USER_AGENT, "portalmmm")) {
			$look= "imode";
		} elseif( stristr($HTTP_USER_AGENT, "V3XML")) {
			$look= "vxml";
		} else {
			$look= "html";
		}
	}
}

$dossier_squelettes= "squelettes/$look";

?>
