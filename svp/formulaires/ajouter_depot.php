<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/svp_depoter');

function formulaires_ajouter_depot_charger_dist(){
	// On ne renvoie pas les valeurs saisies mais on fait un raz systematique
	return array();
}

function formulaires_ajouter_depot_verifier_dist(){
	$erreurs = array();
	$url = _request('url_paquets');

	if (!$url) {
		// L'url est obligatoire
		$erreurs['url_paquets'] = _T('svp:message_nok_champ_obligatoire');
	}
	elseif (!svp_verifier_adresse_depot($url)) {
		// L'url n'est pas correcte, le fichier xml n'a pas ete trouve
		$erreurs['url_paquets'] = _T('svp:message_nok_url_depot_incorrecte', array('url' => $url));
	}
	elseif (sql_countsel('spip_depots','url_paquets='.sql_quote(trim($url)))) {
		// L'url est deja ajoutee
		$erreurs['url_paquets'] = _T('svp:message_nok_depot_deja_ajoute', array('url' => $url));
	}
	return $erreurs;
}

function formulaires_ajouter_depot_traiter_dist(){
	$retour = array();
	$url = _request('url_paquets');

	// On ajoute le depot et ses plugins dans la base
	// On traite le cas d'erreur fichier ($retour['message_erreur']) non conforme
	// - si la syntaxe xml est incorrecte
	// - ou si le depot ne possede pas au moins un plugin
	$ok = svp_ajouter_depot($url, $erreur);

	// Determination des messages de retour
	if (!$ok)
		$retour['message_erreur'] = $erreur;
	else {
		$retour['message_ok'] = _T('svp:message_ok_depot_ajoute', array('url' => $url));
		if (_SVP_LOG_ACTIONS)
				spip_log("ACTION AJOUTER DEPOT (manuel) : url = ". $url, 'svp');
	}
	$retour['editable'] = true;

	return $retour;
}
?>
