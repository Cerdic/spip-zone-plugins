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
function mapimpl_mxn_prive_faire_api_dist()
{
	gmap_ecrire_config('gmap_api_mxn', 'provider', _request('provider'));
	foreach ($GLOBALS['allowed_providers'] as $name => $infos)
	{
		if ($infos['key'] === 'oui')
			gmap_ecrire_config('gmap_api_mxn', 'provider_key_'.$name, _request('provider_key_'.$name));
	}
}

?>
