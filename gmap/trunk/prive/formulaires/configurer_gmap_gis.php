<?php
/*
 * Géolocalisation et cartographie
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2012 - licence GNU/GPL
 *
 * Page de paramétrage principale du plugin
 *
 */

include_spip('inc/gmap_config_utils');

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaire_configurer_gmap_gis_initialiser_dist() {
	gmap_ecrire_config('gmap_api', 'api', "gma3");
}

function formulaires_configurer_gmap_gis_charger_dist() {

	$valeurs = array();
	
	$valeurs['apis'] = gmap_apis_connues();
	$valeurs['api_selected'] = gmap_lire_config('gmap_api', 'api', "gma3");
		
	return $valeurs;
}

function formulaires_configurer_gmap_gis_verifier_dist(){

	$erreurs = array();
	
	return $erreurs;
}

function formulaires_configurer_gmap_gis_traiter_dist(){

	gmap_ecrire_config('gmap_api', 'api', _request('api_code'));
	
	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}

?>
