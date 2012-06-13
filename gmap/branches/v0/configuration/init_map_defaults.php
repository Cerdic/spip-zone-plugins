<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Initialisation de param�tres sur l'interface utilisateur
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

function configuration_init_map_defaults_dist()
{
	// R�cup�rer la liste des APIs
	$apis = gmap_apis_connues();
	
	// Les parcourir
	$profiles = array('interface', 'prive');
	foreach ($apis as $api => $infos)
	{
		$init_map_defaults = charger_fonction("init_map_defaults", "mapimpl/".$api."/prive", true);
		foreach ($profiles as $profile)
		{
			// Charger ce qui est sp�cifique � l'impl�mentation
			if ($init_map_defaults)
				$init_map_defaults($profile);
			
			// Position par d�faut
			$apiConfigKey = 'gmap_'.$api.'_'.$profile;
			gmap_init_config($apiConfigKey, 'default_latitude', "0.0");
			gmap_init_config($apiConfigKey, 'default_longitude', "0.0");
			gmap_init_config($apiConfigKey, 'default_zoom', "1");
		}
	}
}

?>
