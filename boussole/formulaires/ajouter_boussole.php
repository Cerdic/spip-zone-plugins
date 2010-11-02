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
		$url = _request('url_boussole');
		if (!$url) {
			// L'url est obligatoire
			$erreurs['url_boussole'] = _T('boussole:message_nok_champ_obligatoire');
		}
		elseif (!boussole_verifier_adresse($url)) {
			// L'url n'est pas correcte, le fichier xml n'a pas ete trouve
			$erreurs['url_boussole'] = _T('boussole:message_nok_xml_introuvable', array('url' => $url));
		}
		elseif (!boussole_verifier_xml($url)) {
			// Le fichier ne suit pas la DTD
			$erreurs['url_boussole'] = _T('boussole:message_nok_xml_invalide', array('url' => $url));
		}
	}
	return $erreurs;
}

function formulaires_ajouter_boussole_traiter_dist(){
	$retour = array();
	$mode = _request('mode');
	if ($mode == 'standard')
		$url = find_in_path('boussole_spip.xml', 'boussoles/');
	else
		$url = _request('url_boussole');

	// On insere la boussole dans la base
	// et on traite le cas d'erreur fichier ($retour['message_erreur']) non conforme
	$ok = boussole_ajouter($url, $erreur);

	// Determination des messages de retour
	if (!$ok)
		$retour['message_erreur'] = $erreur;
	else {
		$retour['message_ok'] = _T('boussole:message_ok_boussole_ajoutee', array('url' => $url));
		spip_log("ACTION AJOUTER BOUSSOLE : url = ". $url, 'boussole');
	}
	$retour['editable'] = true;

	return $retour;
}
?>
