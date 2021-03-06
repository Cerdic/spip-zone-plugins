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
	if ($mode == 'standard')
		$xml = url_absolue('https://zone.spip.org/trac/spip-zone/export/HEAD/_galaxie_/boussole.spip.org/boussole_spip.xml');

	// On fait des verifications dans traiter pour renvoyer les resultats dans le message d'erreur global
	if (!$url = boussole_localiser_xml($xml)) {
		// Le fichier est introuvable
		$retour['message_erreur'] = _T('boussole:message_nok_xml_introuvable', array('fichier' => $xml));
	}
	else {
		if (!boussole_valider_xml($url, $erreur)) {
			// Le fichier ne suit pas la DTD (boussole.dtd)
			$retour['message_erreur'] = _T('boussole:message_nok_xml_invalide', array('fichier' => $url));
			spip_log("ERREUR DTD" . var_export($erreur['detail'], true), 'boussole' . _LOG_ERREUR);
		}
		else {
			// On insere la boussole dans la base
			// et on traite le cas d'erreur fichier ($retour['message_erreur']) non conforme
			// si c'est encore possible apres avoir valide le fichier avec la dtd
			list($ok, $message) = boussole_ajouter($url);
		
			// Determination des messages de retour
			if (!$ok) {
				$retour['message_erreur'] = $message;
				spip_log("ERREUR AJOUT", 'boussole' . _LOG_ERREUR);
			}
			else {
				$retour['message_ok'] = $message;
				spip_log("ACTION AJOUTER BOUSSOLE : url = ". $url, 'boussole' . _LOG_INFO);
			}
		}
	}
	$retour['editable'] = true;

	return $retour;
}
?>
