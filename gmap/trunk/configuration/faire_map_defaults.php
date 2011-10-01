<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * PArtie active du formulaire du paramétrage de l'interface
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

function configuration_faire_map_defaults_dist()
{
	$result = "";

	// Lire l'API utilisée
	$api = gmap_lire_config('gmap_api', 'api', 'gma3');
	$apiConfigKey = 'gmap_'.$api.'_interface';
	
	// Charger ce qui est spécifique à l'implémentation
	$faire_map_defaults = charger_fonction("faire_map_defaults", "mapimpl/".$api."/prive");
	$msg = $faire_map_defaults();
	
	// Position par défaut
	gmap_ecrire_config($apiConfigKey, 'default_latitude', _request('map_center_latitude'));
	gmap_ecrire_config($apiConfigKey, 'default_longitude', _request('map_center_longitude'));
	gmap_ecrire_config($apiConfigKey, 'default_zoom', _request('map_zoom'));
	
	// Message de retour
	if ($msg != "")
		$result = gmap_ajoute_msg($result, $msg);
	return $result;
}

?>
