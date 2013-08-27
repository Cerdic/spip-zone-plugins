<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_declarer_boussole_manuelle_charger_dist() {
	$valeurs = array();
	return $valeurs;
}


function formulaires_declarer_boussole_manuelle_verifier_dist() {
	$erreurs = array();
	if (!$nom = _request('alias'))
		$erreurs['alias'] = _T('info_obligatoire');
	return $erreurs;
}


function formulaires_declarer_boussole_manuelle_traiter_dist() {
	$retour = array();
	$ok = false;

	$alias_boussole = _request('alias');

	// Vérification que la boussole est bien installée quelque part sur le site
	// -- on cherche donc son fichier XML
	if ($fichier_xml = find_in_path("boussole_traduite-${alias_boussole}.xml")) {
		// Déclaration de la boussole manuelle au serveur
		// -- si elle existe déjà on écrase sa déclaration plutôt que de sortir une erreur
		include_spip('inc/config');
		$boussoles_manuelles = lire_config('boussole/serveur/boussoles_disponibles');
		$boussoles_manuelles[$alias_boussole] = array('prefixe' => '');
		ecrire_config('boussole/serveur/boussoles_disponibles', $boussoles_manuelles);

		// Mise à jour des caches en conséquence
		include_spip('inc/cacher');
		boussole_actualiser_caches();

		$ok = true;
	}
	else {
		$message = _T('boussole:message_nok_declaration_boussole_xml', array('boussole' => $alias_boussole));
	}

	// Determination des messages de retour
	if (!$ok) {
		$retour['message_erreur'] = $message;
		spip_log("Erreur déclaration boussole manuelle $alias_boussole. $message", 'boussole' . _LOG_ERREUR);
	}
	else {
		$retour['message_ok'] = _T('boussole:message_ok_boussole_manuelle_ajoutee', array('boussole' => $alias_boussole));
		spip_log("Déclaration boussole manuelle $alias_boussole ok", 'boussole' . _LOG_INFO);
	}
	$retour['editable'] = true;

	return $retour;
}

?>
