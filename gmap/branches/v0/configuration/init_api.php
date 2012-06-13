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

function configuration_init_api_dist()
{
	// R�cup�rer la liste des APIs
	$apis = gmap_apis_connues();
	
	// Les parcourir
	foreach ($apis as $api => $infos)
	{
		// Charger ce qui est sp�cifique � l'impl�mentation
		$init_api = charger_fonction("init_api", "mapimpl/".$api."/prive");
		if ($init_api)
			$init_api();
	}
}

?>
