<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Test de l'activit� du plugin (par rapport � son param�trage)
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');
include_spip('inc/provider_caps');

// Enregistrement des param�tres pass�s dans la requ�te
function mapimpl_mxn_public_test_actif_dist()
{
	if (gmapmxn_hasCapability('key'))
	{
		$provider = gmap_lire_config('gmap_api_mxn', 'provider', "openlayers");
		$key = gmap_lire_config('gmap_api_mxn', 'provider_key_'.$provider, "");
		return (strlen($key) > 0) ? true : false;
	}
	else
		return true;
}

?>