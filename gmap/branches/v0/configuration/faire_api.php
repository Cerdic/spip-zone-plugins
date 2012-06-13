<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Partie active du formulaire du param�trage de l'API
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

function configuration_faire_api_dist()
{
	// Lire l'API utilis�e
	$api = gmap_lire_config('gmap_api', 'api', 'gma3');
	
	// Charger ce qui est sp�cifique � l'impl�mentation
	$faire_api = charger_fonction("faire_api", "mapimpl/".$api."/prive");
	$faire_api();
}

?>
