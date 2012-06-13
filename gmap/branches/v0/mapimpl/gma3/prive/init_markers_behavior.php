<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Interface de configuration de l'interface pour Google Maps v3
 *
 * Usage :
 * $init_markers_behavior = charger_fonction("init_markers_behavior", "mapimpl/$api/prive");
 * $init_markers_behavior();
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

// Initialisation du paramétrage pour gma3
function mapimpl_gma3_prive_init_markers_behavior_dist()
{
	// InfoBulle
	gmap_init_config('gmap_gma3_interface', 'merge_infos', 'non');
	gmap_init_config('gmap_gma3_interface', 'info_width_percent', '65');
	gmap_init_config('gmap_gma3_interface', 'info_width_absolute', '300');
}

?>
