<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@action_cookie_dist
function action_session_dist() {

	// La cible de notre operation de connexion
	$url = _request('url');
	$redirect = isset($url) ? $url : generer_url_public('session');

	if(isset($GLOBALS['auteur_session'])) {
		session_start();
		$_SESSION['test'] = 1 - $_SESSION['test'];
	}

	// Redirection finale
	redirige_par_entete($redirect, true);
}

?>
