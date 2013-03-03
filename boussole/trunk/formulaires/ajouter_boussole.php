<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/deboussoler');

function formulaires_ajouter_boussole_charger_dist(){
	return array('mode' => _request('mode'),
				'url_boussole' => _request('url_boussole'));
}


function formulaires_ajouter_boussole_verifier_dist(){
	$erreurs = array();
	$mode = _request('mode');

	if ($mode == 'url_perso') {
		// Pour le mode perso uniquement on verifie que le fichier a bien ete saisi
		$url = _request('url_boussole');
		if (!$url)
			// L'url est obligatoire
			$erreurs['url_boussole'] = _T('boussole:message_nok_champ_obligatoire');
	}
	
	return $erreurs;
}


function formulaires_ajouter_boussole_traiter_dist(){
	$retour = array();
	$mode = _request('mode');
	$xml = _request('url_boussole');

	// Cas de la boussole SPIP
	if ($mode == 'standard') {
		$boussole = 'spip';
		$serveur = 'spip';
	}
	else {
		$boussole = 'spip';
		$serveur = 'spip';
	}

	// On insere la boussole dans la base
	list($ok, $message) = boussole_ajouter($boussole, $serveur);
		
	// Determination des messages de retour
	if (!$ok) {
		$retour['message_erreur'] = $message;
		spip_log("Ajout manuel : erreur lors de l'insertion de la boussole $boussole", 'boussole' . _LOG_ERREUR);
	}
	else {
		$retour['message_ok'] = $message;
		spip_log("Ajout manuel ok de la boussole $boussole", 'boussole' . _LOG_INFO);
	}
	$retour['editable'] = true;

	return $retour;
}
?>
