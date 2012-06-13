<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Interface de configuration de l'API pour Google Maps v3
 *
 * Usage :
 * $faire_api = charger_fonction("faire_api", "mapimpl/$api/prive");
 * $faire_api();
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

// Enregistrement des paramètres passés dans la requête
function mapimpl_gma3_prive_faire_api_dist()
{
	gmap_ecrire_config('gmap_api_gma3', 'key', _request('cle_api'));
	$version = _request('api_version');
	if ($version == "3.")
		$version .= _request('num_other_version');
	gmap_ecrire_config('gmap_api_gma3', 'version', $version);
}

?>
