<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Partie active du formulaire du paramétrage de l'API
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

function configuration_faire_api_dist()
{
	// Lire l'API utilisée
	$api = gmap_lire_config('gmap_api', 'api', 'gma3');
	
	// Charger ce qui est spécifique à l'implémentation
	$faire_api = charger_fonction("faire_api", "mapimpl/".$api."/prive");
	$faire_api();
}

?>
