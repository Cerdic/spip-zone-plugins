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

function formulaire_configurer_gmap_optimisations_initialiser_dist() {

	gmap_init_config('gmap_optimisations', 'gerer_selection', 'oui');
	gmap_init_config('gmap_optimisations', 'gerer_branches', 'oui');
}

function formulaires_configurer_gmap_optimisations_charger_dist(){

	$valeurs = array();

	$valeurs['gerer_selection'] = gmap_lire_config('gmap_optimisations', 'gerer_selection', 'oui');
	$valeurs['gerer_branches'] = gmap_lire_config('gmap_optimisations', 'gerer_branches', 'oui');
	
	return $valeurs;
}

function formulaires_configurer_gmap_optimisations_verifier_dist(){

	$erreurs = array();
	
	return $erreurs;
}

function formulaires_configurer_gmap_optimisations_traiter_dist(){

	// Récupérer les paramètres et sauvegarder
	gmap_ecrire_config('gmap_optimisations', 'gerer_selection', ((_request('gerer_selection') === 'oui') ? 'oui' : 'non'));
	gmap_ecrire_config('gmap_optimisations', 'gerer_branches', ((_request('gerer_branches') === 'oui') ? 'oui' : 'non'));

	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}

?>
