<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Interface de configuration de l'interface pour Google Maps v2
 *
 * Usage :
 * $faire_markers_behavior = charger_fonction("faire_markers_behavior", "mapimpl/$api/prive");
 * $faire_markers_behavior();
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

// Enregistrement des param�tres pass�s dans la requ�te
function mapimpl_gma2_prive_faire_markers_behavior_dist()
{
	// InfoBulle
	gmap_ecrire_config('gmap_gma2_interface', 'merge_infos', _request('merge_infos'));
	gmap_ecrire_config('gmap_gma2_interface', 'info_width_percent', _request('info_width_percent'));
	gmap_ecrire_config('gmap_gma2_interface', 'info_width_absolute', _request('info_width_absolute'));
	
	return "";
}

?>
