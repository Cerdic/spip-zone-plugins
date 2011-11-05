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

// Initialisation du paramétrage pour mxn
function mapimpl_mxn_prive_init_map_defaults_dist()
{
	gmap_init_config('gmap_mxn_interface', 'type_defaut', "mixte");
	gmap_init_config('gmap_mxn_interface', 'zoom_control', "large");
	gmap_init_config('gmap_mxn_interface', 'pan_control', "oui");
	gmap_init_config('gmap_mxn_interface', 'scale_control', "oui");
	gmap_init_config('gmap_mxn_interface', 'overview_control', "non");
	gmap_init_config('gmap_mxn_interface', 'types_control', "oui");
}

?>
