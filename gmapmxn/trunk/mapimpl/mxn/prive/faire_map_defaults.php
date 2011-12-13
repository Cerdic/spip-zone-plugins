<?php
/*
 * Extension Mapstraction pour GMap
 *
 * Auteur : Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

// Enregistrement des paramètres passés dans la requête
function mapimpl_mxn_prive_faire_map_defaults_dist($profile = 'interface')
{
	// Clefs d'accès aux valeurs enregistrées
	if (!isset($profile))
		$profile = 'interface';
	$apiConfigKey = 'gmap_mxn_'.$profile;
	
	gmap_ecrire_config($apiConfigKey, 'type_defaut', _request('type_carte_defaut'));
	gmap_ecrire_config($apiConfigKey, 'zoom_control', _request('zoom_control'));
	gmap_ecrire_config($apiConfigKey, 'pan_control', ((_request('pan_control') === "oui") ? "oui" : "non"));
	gmap_ecrire_config($apiConfigKey, 'scale_control', ((_request('scale_control') === "oui") ? "oui" : "non"));
	gmap_ecrire_config($apiConfigKey, 'overview_control', ((_request('overview_control') === "oui") ? "oui" : "non"));
	gmap_ecrire_config($apiConfigKey, 'types_control', ((_request('types_control') === "oui") ? "oui" : "non"));
}

?>
