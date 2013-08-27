<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_declarer_serveur_boussoles_charger_dist() {
	$valeurs = array();
	return $valeurs;
}


function formulaires_declarer_serveur_boussoles_verifier_dist() {
	$erreurs = array();
	if (!$nom = _request('url'))
		$erreurs['url'] = _T('info_obligatoire');
	return $erreurs;
}


function formulaires_declarer_serveur_boussoles_traiter_dist() {
	$retour = array();
	$ok = false;

	$url_serveur = _request('url');
	$action = rtrim($url_serveur, '/')
			. "/spip.php?action=serveur_lister_boussoles";
	include_spip('inc/distant');
	$page = recuperer_page($action);

	$convertir = charger_fonction('simplexml_to_array', 'inc');
	$tableau = $convertir(simplexml_load_string($page), false);
	$tableau = $tableau['root'];

	if (isset($tableau['name'])
	AND ($tableau['name'] == 'boussoles')) {
		$serveur = $tableau['attributes']['serveur'];

		include_spip('inc/config');
		$serveurs = lire_config('boussole/client/serveurs_disponibles');
		$serveurs[$serveur] = array('url' => $url_serveur);
		ecrire_config('boussole/client/serveurs_disponibles', $serveurs);

		$ok = true;
	}
	else if (isset($tableau['name'])
		AND ($tableau['name'] == 'erreur')) {
		$message = _T("boussole:message_nok_{$tableau['attributes']['id']}", array('serveur' => $_serveur));
	}
	else {
		$message = _T('boussole:message_nok_reponse_invalide', array('serveur' => $_serveur));
	}

	// Determination des messages de retour
	if (!$ok) {
		$retour['message_erreur'] = $message;
		spip_log("Erreur ajout serveur $serveur (url : $url_serveur). $message", 'boussole' . _LOG_ERREUR);
	}
	else {
		$retour['message_ok'] = _T('boussole:message_ok_serveur_ajoute', array('serveur' => $serveur, 'url' => $url_serveur));
		spip_log("Ajout serveur $serveur ok (url : $url_serveur)", 'boussole' . _LOG_INFO);
	}
	$retour['editable'] = true;

	return $retour;
}

?>
