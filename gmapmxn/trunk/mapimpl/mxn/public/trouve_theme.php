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
include_spip('inc/provider_caps');

// Enregistrement des paramètres passés dans la requête
function mapimpl_mxn_public_trouve_theme_dist()
{
	$provider = gmap_lire_config('gmap_api_mxn', 'provider', "openlayers");
	$providerCaps = gmapmxn_getProviderCaps($provider);
	return $providerCaps['theme'];
}

?>