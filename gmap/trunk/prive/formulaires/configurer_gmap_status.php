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

function formulaires_configurer_gmap_status_charger_dist() {

	$valeurs = array();

	$valeurs['actif'] = gmap_est_actif();
	
	$api = gmap_lire_api();
	$apis = gmap_apis_connues();
	if ($apis && $apis[$api])
		$valeurs['api_desc'] = $apis[$api]['name'];
	else
		$valeurs['api_desc'] = "API ".$api;

	return $valeurs;
}

?>
