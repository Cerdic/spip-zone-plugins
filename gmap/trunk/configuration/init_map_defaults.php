<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Initialisation de paramètres sur l'interface utilisateur
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

function configuration_init_map_defaults_dist()
{
	// Récupérer la liste des APIs
	$apis = gmap_apis_connues();
	
	// Les parcourir
	foreach ($apis as $api => $infos)
	{
		$apiConfigKey = 'gmap_'.$api.'_interface';
		
		// Charger ce qui est spécifique à l'implémentation
		$init_map_defaults = charger_fonction("init_map_defaults", "mapimpl/".$api."/prive");
		$init_map_defaults();
		
		// Position par défaut
		gmap_init_config($apiConfigKey, 'default_latitude', "0.0");
		gmap_init_config($apiConfigKey, 'default_longitude', "0.0");
		gmap_init_config($apiConfigKey, 'default_zoom', "1");
	}
}

?>
