<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_aeres_charger_dist(){
	if (isset($GLOBALS['meta']['aeres']))
		$valeurs = unserialize($GLOBALS['meta']['aeres']);
	else
		$valeurs = array(
			'debut' => '',
			'fin' => '',
			'csl' => '',
			'conference_actes' => '',
			'titre_biblio_unite' => '',
			'autorisation_verif_type' => '',
			'autorisation_verif_statuts' => '',
			'autorisation_verif_auteurs' => '',
			'autorisation_biblio_unite_type' => '',
			'autorisation_biblio_unite_statuts' => '',
			'autorisation_biblio_unite_auteurs' => '',
			'autorisation_stats_type' => '',
			'autorisation_stats_statuts' => '',
			'autorisation_stats_auteurs' => ''
		);
	
	return $valeurs;
}

function formulaires_configurer_aeres_verifier_dist(){
	$erreurs = array();
	if (!_request('debut') || !intval(_request('debut'))) $erreurs['debut'] = 'Vous devez spécifier un nombre entier.';
	if (!_request('fin') || !intval(_request('fin'))) $erreurs['fin'] = 'Vous devez spécifier un nombre entier.';
	if (!autoriser('webmestre')) $erreurs['message_erreur'] = 'Vous n\'avez pas les droits suffisants pour modifier la configuration.';
	return $erreurs;
}



function formulaires_configurer_aeres_traiter_dist(){
	if (isset($GLOBALS['meta']['aeres']))
		$config = unserialize($GLOBALS['meta']['aeres']);
	else
		$config = array();
	
	$config['debut'] = _request('debut');
	$config['fin'] = _request('fin');
	$config['csl'] = _request('csl');
	$config['conference_actes'] = _request('conference_actes');
	$config['titre_biblio_unite'] = _request('titre_biblio_unite');
	$config['autorisation_verif_type'] = _request('autorisation_verif_type');
	$config['autorisation_verif_statuts'] = _request('autorisation_verif_statuts');
	$config['autorisation_verif_auteurs'] = _request('autorisation_verif_auteurs');
	$config['autorisation_biblio_unite_type'] = _request('autorisation_biblio_unite_type');
	$config['autorisation_biblio_unite_statuts'] = _request('autorisation_biblio_unite_statuts');
	$config['autorisation_biblio_unite_auteurs'] = _request('autorisation_biblio_unite_auteurs');
	$config['autorisation_stats_type'] = _request('autorisation_stats_type');
	$config['autorisation_stats_statuts'] = _request('autorisation_stats_statuts');
	$config['autorisation_stats_auteurs'] = _request('autorisation_stats_auteurs');

	include_spip('inc/meta');
	ecrire_meta('aeres',serialize($config));
	
	return array('message_ok'=>_T('config_info_enregistree'));
}

?>