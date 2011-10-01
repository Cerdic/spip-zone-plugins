<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Interface de configuration du comportement des marqueurs pour Google Maps v2
 *
 * Usage :
 * $show_markers_behavior = charger_fonction("show_markers_behavior", "mapimpl/$api/prive");
 * $show_markers_behavior();
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

// Enregistrement des paramètres passés dans la requête
function mapimpl_gma2_prive_show_markers_behavior_dist()
{
	$corps = "";
	
	// Paramètres des info-bulles
	$merge_infos = gmap_lire_config('gmap_gma2_interface', 'merge_infos', 'non');
	$info_width_percent = gmap_lire_config('gmap_gma2_interface', 'info_width_percent', '65');
	$info_width_absolute = gmap_lire_config('gmap_gma2_interface', 'info_width_absolute', '300');
	$corps .= '
<fieldset id="config_info_bulle" class="config_group">
	<legend>'._T('gmap:configuration_info_bulle').'</legend>
	<div class="padding"><div class="interior">
		<p class="suivi"><input type="checkbox" name="merge_infos" id="merge_infos" value="oui"'.(($merge_infos==="oui")?'checked="checked"':'').' /><label for="merge_infos">'._T('gmap:choix_info_merge').'</label></p>
		<p class="explications droite">'._T('gmap:explication_info_merge_more').'</p>
		<p class="suivi">'._T('gmap:explication_info_width').'<br />
		<label for="info_width_percent">'._T('gmap:explication_info_width_percent').'</label>&nbsp;<input type="text" name="info_width_percent" class="text" value="'.$info_width_percent.'" id="info_width_percent" width="5" />&nbsp;<i>'._T('gmap:explication_info_width_zero').'</i><br />
		<label for="info_width_absolute">'._T('gmap:explication_info_width_absolute').'</label>&nbsp;<input type="text" name="info_width_absolute" class="text" value="'.$info_width_absolute.'" id="info_width_absolute" width="5" />&nbsp;<i>'._T('gmap:explication_info_width_zero').'</i></p>
		<p class="explications droite">'._T('gmap:explication_info_width_more').'</p>
	</div></div>
</fieldset>' . "\n";

	return $corps;
}

?>
