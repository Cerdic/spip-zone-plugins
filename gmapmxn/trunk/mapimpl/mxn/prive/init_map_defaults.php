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

// Initialisation du paramétrage pour mxn
function mapimpl_mxn_prive_init_map_defaults_dist($profile = 'interface')
{
	// Clefs d'accès aux valeurs enregistrées
	if (!isset($profile))
		$profile = 'interface';
	$apiConfigKey = 'gmap_mxn_'.$profile;

	gmap_init_config($apiConfigKey, 'type_defaut', "mixte");
	gmap_init_config($apiConfigKey, 'zoom_control', "large");
	gmap_init_config($apiConfigKey, 'pan_control', "oui");
	gmap_init_config($apiConfigKey, 'scale_control', "oui");
	gmap_init_config($apiConfigKey, 'overview_control', "non");
	gmap_init_config($apiConfigKey, 'types_control', "oui");
}

?>
