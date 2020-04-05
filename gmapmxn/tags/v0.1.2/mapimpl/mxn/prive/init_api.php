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

// Initialisation du paramétrage pour gma3
function mapimpl_mxn_prive_init_api_dist()
{
	gmap_init_config('gmap_api_mxn', 'provider', "openlayers");
	foreach ($GLOBALS['allowed_providers'] as $name => $infos)
	{
		if ($infos['key'] === 'oui')
			gmap_lire_config('gmap_api_mxn', 'provider_key_'.$name, "");
	}
}

?>
