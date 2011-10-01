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
 * $show_map_defaults = charger_fonction("show_map_defaults", "mapimpl/$api/prive");
 * $show_map_defaults();
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

// Éléments de l'interface
function _gmap_get_gma2_ui_elements()
{
	$corps = "";
	
	// Fonds de cartes
	$allow_type_plan = gmap_lire_config('gmap_gma2_interface', 'type_carte_plan', "oui");
	$allow_type_satellite = gmap_lire_config('gmap_gma2_interface', 'type_carte_satellite', "oui");
	$allow_type_mixte = gmap_lire_config('gmap_gma2_interface', 'type_carte_mixte', "oui");
	$allow_type_physic = gmap_lire_config('gmap_gma2_interface', 'type_carte_physic', "oui");
	$allow_type_earth = gmap_lire_config('gmap_gma2_interface', 'type_carte_earth', "oui");
	$default_type = gmap_lire_config('gmap_gma2_interface', 'type_defaut', "mixte");
	$corps .= '
<fieldset id="config_carte_fonds" class="config_group">
	<legend>'._T('gmap:configuration_defaults_types').'</legend>
	<div class="padding"><div class="interior">
		<label for="type_carte_defaut">'._T('gmap:explication_type_carte_defaut').'</label>
		<select name="type_carte_defaut" id="type_carte_defaut" class="tracked">
			<option value="plan"'.(($default_type === "plan")?' selected="selected"':'').'>'._T('gmap:type_carte_plan').'</option>
			<option value="satellite"'.(($default_type === "satellite")?' selected="selected"':'').'>'._T('gmap:type_carte_satellite').'</option>
			<option value="mixte"'.(($default_type === "mixte")?' selected="selected"':'').'>'._T('gmap:type_carte_mixte').'</option>
			<option value="physic"'.(($default_type === "physic")?' selected="selected"':'').'>'._T('gmap:type_carte_physic').'</option>
			// On ne propose pas Google Earth : il faut le plugin, donc pour une config par défaut c\'est un peu violent...
			<option value="earth"'.(($default_type === "earth")?' selected="selected"':'').'>'._T('gmap:type_carte_earth').'</option>
		</select>
		<p>'._T('gmap:explication_types_cartes_visibles').'</p>
		<div class="liste_choix">
			<input type="checkbox" name="type_carte_plan" id="type_carte_plan" class="tracked" value="oui"'.(($allow_type_plan==="oui")?'checked="checked"':'').' /><label for="type_carte_plan">'._T('gmap:choix_type_carte_plan').'</label><br/>
			<input type="checkbox" name="type_carte_satellite" id="type_carte_satellite" class="tracked" value="oui"'.(($allow_type_satellite==="oui")?'checked="checked"':'').' /><label for="type_carte_satellite">'._T('gmap:choix_type_carte_satellite').'</label><br/>
			<input type="checkbox" name="type_carte_mixte" id="type_carte_mixte" class="tracked" value="oui"'.(($allow_type_mixte==="oui")?'checked="checked"':'').' /><label for="type_carte_mixte">'._T('gmap:choix_type_carte_mixte').'</label><br/>
			<input type="checkbox" name="type_carte_physic" id="type_carte_physic" class="tracked" value="oui"'.(($allow_type_physic==="oui")?'checked="checked"':'').' /><label for="type_carte_physic">'._T('gmap:choix_type_carte_physic').'</label><br/>
			<input type="checkbox" name="type_carte_earth" id="type_carte_earth" class="tracked" value="oui"'.(($allow_type_earth==="oui")?'checked="checked"':'').' /><label for="type_carte_earth">'._T('gmap:choix_type_carte_earth').'</label>
			<input type="hidden" name="none" id="current_type" value="oui" />
		</div>
	</div></div>
</fieldset>' . "\n";
	
	// Script de cohérence des types : quand un type est sélectionné, on le check et le grise
	$corps .= '<script type="text/javascript">'."\n".'	//<![CDATA['."\n";
	$corps .= '
function updateDefaultMapType()
{
	jQuery("#current_type").attr("name", "none");
	function _activate(id, activ)
	{
		if (activ)
		{
			jQuery("#"+id).attr("checked","checked");
			jQuery("#"+id).attr("disabled","disabled");
			jQuery("#"+id).attr("name","disabled");
			jQuery("#current_type").attr("name", id);
		}
		else
		{
			jQuery("#"+id).attr("name",id);
			jQuery("#"+id).removeAttr("disabled");
		}
	}
	var type = jQuery("#type_carte_defaut").val();
	_activate("type_carte_plan", (type == "plan") ? true : false);
	_activate("type_carte_satellite", (type == "satellite") ? true : false);
	_activate("type_carte_mixte", (type == "mixte") ? true : false);
	_activate("type_carte_physic", (type == "physic") ? true : false);
}
jQuery("#type_carte_defaut").change(function() { updateDefaultMapType(); });
jQuery(document).ready(function() { updateDefaultMapType(); });
';
	$corps .= '	//]]>'."\n".'</script>'."\n";
	
	// Choix du type de contrôles
	$types_control_style = gmap_lire_config('gmap_gma2_interface', 'types_control_style', "menu");
	$nav_control_style = gmap_lire_config('gmap_gma2_interface', 'nav_control_style', "3D");
	$allow_dblclk_zoom = gmap_lire_config('gmap_gma2_interface', 'allow_dblclk_zoom', "non");
	$allow_continuous_zoom = gmap_lire_config('gmap_gma2_interface', 'allow_continuous_zoom', "non");
	$allow_wheel_zoom = gmap_lire_config('gmap_gma2_interface', 'allow_wheel_zoom', "non");
	$corps .= '
<fieldset id="config_carte_params" class="config_group">
	<legend>'._T('gmap:configuration_defaults_controls').'</legend>
	<div class="padding"><div class="interior">
		<p><label for="nav_control_style">'._T('gmap:choix_style_control_types').'</label>
		<select name="types_control_style" id="types_control_style" class="tracked">
			<option value="none"'.(($types_control_style === "none")?' selected="selected"':'').'>'._T('gmap:style_types_control_none').'</option>
			<option value="button"'.(($types_control_style === "button")?' selected="selected"':'').'>'._T('gmap:style_types_control_button').'</option>
			<option value="menu"'.(($types_control_style === "menu")?' selected="selected"':'').'>'._T('gmap:style_types_control_menu').'</option>
		</select></p>
		<p><label for="nav_control_style">'._T('gmap:choix_style_control_nav').'</label>
		<select name="nav_control_style" id="nav_control_style" class="tracked">
			<option value="none"'.(($nav_control_style === "none")?' selected="selected"':'').'>'._T('gmap:style_nav_control_none').'</option>
			<option value="small"'.(($nav_control_style === "small")?' selected="selected"':'').'>'._T('gmap:style_nav_control_small').'</option>
			<option value="large"'.(($nav_control_style === "large")?' selected="selected"':'').'>'._T('gmap:style_nav_control_large').'</option>
			<option value="3D"'.(($nav_control_style === "3D")?' selected="selected"':'').'>'._T('gmap:style_nav_control_3D').'</option>
		</select></p>
		<p><input type="checkbox" name="allow_dblclk_zoom" id="allow_dblclk_zoom" class="tracked" value="oui"'.(($allow_dblclk_zoom==="oui")?'checked="checked"':'').' /><label for="allow_dblclk_zoom">'._T('gmap:choix_zoom_dblclk').'</label><br />
		<input type="checkbox" name="allow_continuous_zoom" id="allow_continuous_zoom" class="tracked" value="oui"'.(($allow_continuous_zoom==="oui")?'checked="checked"':'').' /><label for="allow_continuous_zoom">'._T('gmap:choix_zoom_continuous').'</label><br />
		<input type="checkbox" name="allow_wheel_zoom" id="allow_wheel_zoom" class="tracked" value="oui"'.(($allow_wheel_zoom==="oui")?'checked="checked"':'').' /><label for="allow_wheel_zoom">'._T('gmap:choix_zoom_wheel').'</label></p>
	</div></div>
</fieldset>' . "\n";
	
	return $corps;
}

// Fonction qui lit les paramètres de la carte depuis l'interface ci-dessus
function _gmap_get_gma2_get_params()
{
	// Script spécifique pour lire le paramétrage
	$getParams = '
// Lire les paramètres de la carte dans les éléments de formulaire
function getParams(bIncludeViewport)
{
	var params = new Object();
	
	// Fonds de carte
	params.mapTypes = new  Array();
	if (jQuery("#type_carte_plan:checked").val() == "oui") params.mapTypes.push("plan");
	if (jQuery("#type_carte_satellite:checked").val() == "oui") params.mapTypes.push("satellite");
	if (jQuery("#type_carte_mixte:checked").val() == "oui") params.mapTypes.push("mixte");
	if (jQuery("#type_carte_physic:checked").val() == "oui") params.mapTypes.push("physic");
	if (jQuery("#type_carte_earth:checked").val() == "oui") params.mapTypes.push("earth");
	params["defaultMapType"] = jQuery("#type_carte_defaut").val();
	
	// Autres paramètres
	params["styleBackgroundCommand"] = jQuery("#types_control_style").val();
	params["styleNavigationCommand"] = jQuery("#nav_control_style").val();
	params["enableDblClkZoom"] = (jQuery("#allow_dblclk_zoom:checked").val() == "oui") ? true : false;
	params["enableContinuousZoom"] = (jQuery("#allow_continuous_zoom:checked").val() == "oui") ? true : false;
	params["enableWheelZoom"] = (jQuery("#allow_wheel_zoom:checked").val() == "oui") ? true : false;
	
	// Position par défaut
	if (bIncludeViewport)
	{
		params["viewLatitude"] = parseFloat(jQuery("#map_center_latitude").val());
		params["viewLongitude"] = parseFloat(jQuery("#map_center_longitude").val());
		params["viewZoom"] = parseFloat(jQuery("#map_zoom").val());
	}
	
	return params;
}
';

	return $getParams;
}

// Enregistrement des paramètres passés dans la requête
function mapimpl_gma2_prive_show_map_defaults_dist(&$uiElements, &$getParams)
{
	$uiElements = _gmap_get_gma2_ui_elements();
	$getParams = _gmap_get_gma2_get_params();
	return true;
}

?>
