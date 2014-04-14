<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_declarer_serveur_boussoles_charger_dist() {
	$valeurs = array();
	return $valeurs;
}


function formulaires_declarer_serveur_boussoles_verifier_dist() {
	$erreurs = array();
	if (!_request('url'))
		$erreurs['url'] = _T('info_obligatoire');
	return $erreurs;
}


function formulaires_declarer_serveur_boussoles_traiter_dist() {
	$retour = array();
	$ok = false;
	$message = '';
	$serveur = '';

	$url_serveur = _request('url');
	$action = rtrim($url_serveur, '/')
			. "/spip.php?action=serveur_lister_boussoles";
	include_spip('inc/distant');
	$page = recuperer_page($action);

	$convertir = charger_fonction('xml_decode', 'inc');
	$tableau = $convertir($page);

	// On vérifie que le serveur héberge bien au moins une boussole
	if (isset($tableau['boussoles'])) {
		$serveur = $tableau['boussoles']['@attributes']['serveur'];

		include_spip('inc/config');
		$serveurs = lire_config('boussole/client/serveurs_disponibles');
		$serveurs[$serveur] = array('url' => $url_serveur);
		ecrire_config('boussole/client/serveurs_disponibles', $serveurs);

		$ok = true;
	}
	else if (isset($tableau['erreur'])) {
		$serveur = $tableau['erreur']['@attributes']['serveur'];
		$message = _T("boussole:message_nok_{$tableau['erreur']['@attributes']['id']}", array('serveur' => $serveur));
	}
	else {
		$message = _T('boussole:message_nok_reponse_invalide', array('serveur' => $url_serveur));
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
