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

// Enregistrement des paramètres passés dans la requête
function mapimpl_mxn_prive_faire_map_defaults_dist()
{
	gmap_ecrire_config('gmap_mxn_interface', 'type_defaut', _request('type_carte_defaut'));
	gmap_ecrire_config('gmap_mxn_interface', 'zoom_control', _request('zoom_control'));
	gmap_ecrire_config('gmap_mxn_interface', 'pan_control', ((_request('pan_control') === "oui") ? "oui" : "non"));
	gmap_ecrire_config('gmap_mxn_interface', 'scale_control', ((_request('scale_control') === "oui") ? "oui" : "non"));
	gmap_ecrire_config('gmap_mxn_interface', 'overview_control', ((_request('overview_control') === "oui") ? "oui" : "non"));
	gmap_ecrire_config('gmap_mxn_interface', 'types_control', ((_request('types_control') === "oui") ? "oui" : "non"));
	return "";
}

?>
