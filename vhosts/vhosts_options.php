<?php

global $dossier_squelettes, $requiredHost, $vhosts;

if (!defined('_NOM_VHOST')) {
	// On calcule une fois pour toute quel est le squelette a interpreter

	// par défaut, les squelettes sont a la racine du repertoire squelettes
	// attention, cela signifie que le choix des squelettes par vhost ne marche que
	// pour ceux presents dans ce repertoires, et pas ceux dans d'eventuels plugins
	$requiredHost = '';

	if (lire_fichier(_DIR_SESSIONS . 'vhosts.txt', $vhosts)) {
		$vhosts = @unserialize($vhosts);
	} else {
 		$vhosts = array();
	}
	spip_log("vhosts = ".var_export($vhosts, 1));

	if(array_key_exists("HOST", $_GET)) {
		// on fixe de force si la valeur est dans l'url
		$requiredHost = $_GET["HOST"];
		// on enleve cet argument de l'url, pour pas polluer les liens qui
		// seront generes par la suite
		str_replace("&HOST=$requiredHost", '', $GLOBALS['REQUEST_URI']);
		str_replace("?HOST=$requiredHost", '', $GLOBALS['REQUEST_URI']);

		// slashs interdits => dans ce cas, on ne le met pas dans le cookie
		if (strstr($requiredHost, '/')) {
			$requiredHost = '';
		} else {
			setcookie("HOST", $requiredHost);
		}
	} elseif(!empty($_COOKIE["HOST"])) {
		// sinon, on la cherche dans un cookie
		$requiredHost = $_COOKIE["HOST"];
		// slashs interdits, meme en tripotant les cookies
		if (strstr($requiredHost, '/')) {
			$requiredHost = '';
		}
	} else {
		// sinon, selon le HTTP_HOST
		// Verification pour le domaine general (pour traiter plusieurs sous-domaines d'un seul coup)
		$domaine = implode('.',array_slice(explode('.',strtolower($_SERVER['HTTP_HOST'])),-2,2));
		if(array_key_exists(strtolower($_SERVER['HTTP_HOST']), $vhosts)) {
			$requiredHost = $vhosts[$_SERVER['HTTP_HOST']];
		} elseif (array_key_exists($domaine, $vhosts) || is_dir(_DIR_RACINE."squelettes/$domaine")) {
			$requiredHost  = $vhosts[$domaine]?$vhosts[$domaine]:$domaine;
		} else {
			$requiredHost = '';
		}
	}

	spip_log("requiredHost=$requiredHost");
	if ($requiredHost) {
		@define('_NOM_VHOST',$requiredHost);
		$dossier_squelettes = "squelettes/$requiredHost";
	}
}

?>
