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

// Ajout d'une combo pour la position du control
function _gmap_add_control_position_choice($currentPosition, $varName)
{
	$corps .= '
		<select name="'.$varName.'" id="'.$varName.'" class="tracked">
			<option value="TL"'.(($currentPosition === "TL")?' selected="selected"':'').'>'._T('gmap:control_position_topleft').'</option>
			<option value="TC"'.(($currentPosition === "TC")?' selected="selected"':'').'>'._T('gmap:control_position_topcenter').'</option>
			<option value="TR"'.(($currentPosition === "TR")?' selected="selected"':'').'>'._T('gmap:control_position_topright').'</option>
			<option value="RT"'.(($currentPosition === "RT")?' selected="selected"':'').'>'._T('gmap:control_position_righttop').'</option>
			<option value="RC"'.(($currentPosition === "RC")?' selected="selected"':'').'>'._T('gmap:control_position_rightcenter').'</option>
			<option value="RB"'.(($currentPosition === "RB")?' selected="selected"':'').'>'._T('gmap:control_position_rightbottom').'</option>
			<option value="BR"'.(($currentPosition === "BR")?' selected="selected"':'').'>'._T('gmap:control_position_bottomright').'</option>
			<option value="BC"'.(($currentPosition === "BC")?' selected="selected"':'').'>'._T('gmap:control_position_bottomcenter').'</option>
			<option value="BL"'.(($currentPosition === "BL")?' selected="selected"':'').'>'._T('gmap:control_position_bottomleft').'</option>
			<option value="LB"'.(($currentPosition === "LB")?' selected="selected"':'').'>'._T('gmap:control_position_leftbottom').'</option>
			<option value="LC"'.(($currentPosition === "LC")?' selected="selected"':'').'>'._T('gmap:control_position_leftcenter').'</option>
			<option value="LT"'.(($currentPosition === "LT")?' selected="selected"':'').'>'._T('gmap:control_position_lefttop').'</option>
		</select>';
	return $corps;
}

// Éléments de l'interface
function _gmap_get_gma3_ui_elements()
{
	$corps = "";
	
	// Test si on peut proposer l'interface Google Earth
	$isEarth = (strlen(gmap_lire_config('gmap_api_gma3', 'key', "")) > 0) ? true : false;

	// Fonds de cartes
	$allow_type_plan = gmap_lire_config('gmap_gma3_interface', 'type_carte_plan', "oui");
	$allow_type_satellite = gmap_lire_config('gmap_gma3_interface', 'type_carte_satellite', "oui");
	$allow_type_mixte = gmap_lire_config('gmap_gma3_interface', 'type_carte_mixte', "oui");
	$allow_type_physic = gmap_lire_config('gmap_gma3_interface', 'type_carte_physic', "oui");
	$allow_type_earth = $isEarth ? gmap_lire_config('gmap_gma3_interface', 'type_carte_earth', "oui") : "non";
	$default_type = gmap_lire_config('gmap_gma3_interface', 'type_defaut', "mixte");
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
		</select>
		<p>'._T('gmap:explication_types_cartes_visibles').'</p>
		<div class="liste_choix">
			<input type="checkbox" name="type_carte_plan" id="type_carte_plan" class="tracked" value="oui"'.(($allow_type_plan==="oui")?'checked="checked"':'').' /><label for="type_carte_plan">'._T('gmap:choix_type_carte_plan').'</label><br/>
			<input type="checkbox" name="type_carte_satellite" id="type_carte_satellite" class="tracked" value="oui"'.(($allow_type_satellite==="oui")?'checked="checked"':'').' /><label for="type_carte_satellite">'._T('gmap:choix_type_carte_satellite').'</label><br/>
			<input type="checkbox" name="type_carte_mixte" id="type_carte_mixte" class="tracked" value="oui"'.(($allow_type_mixte==="oui")?'checked="checked"':'').' /><label for="type_carte_mixte">'._T('gmap:choix_type_carte_mixte').'</label><br/>
			<input type="checkbox" name="type_carte_physic" id="type_carte_physic" class="tracked" value="oui"'.(($allow_type_physic==="oui")?'checked="checked"':'').' /><label for="type_carte_physic">'._T('gmap:choix_type_carte_physic').'</label><br/>';
	if ($isEarth)
		$corps .= '
			<input type="checkbox" name="type_carte_earth" id="type_carte_earth" class="tracked" value="oui"'.(($allow_type_earth==="oui")?'checked="checked"':'').' /><label for="type_carte_earth">'._T('gmap:choix_type_carte_earth').'</label><br/>';
	$corps .= '
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
	$corps .= '
<fieldset id="config_carte_params" class="config_group">
	<legend>'._T('gmap:configuration_defaults_controls').'</legend>
	<div class="padding"><div class="interior">';
	// Contrôle du type de carte
	$types_control_style = gmap_lire_config('gmap_gma3_interface', 'types_control_style', "menu");
	$types_control_position = gmap_lire_config('gmap_gma3_interface', 'types_control_position', "TR");
	$corps .= '
		<p class="suivi"><label for="types_control_style">'._T('gmap:choix_style_control_types').'</label>
		<select name="types_control_style" id="types_control_style" class="tracked suivant">
			<option value="none"'.(($types_control_style === "none")?' selected="selected"':'').'>'._T('gmap:style_types_control_none').'</option>
			<option value="button"'.(($types_control_style === "button")?' selected="selected"':'').'>'._T('gmap:style_types_control_button').'</option>
			<option value="menu"'.(($types_control_style === "menu")?' selected="selected"':'').'>'._T('gmap:style_types_control_menu').'</option>
		</select>';
	$corps .= _gmap_add_control_position_choice($types_control_position, "types_control_position");
	$corps .= '
		</p>';
	// Navigation : zoom & pan
	// Le contrôle Navigation a disparu de l'API GoogleMaps, remplacé par zoom + pan
	/*$nav_control_style = gmap_lire_config('gmap_gma3_interface', 'nav_control_style', "auto");
	$nav_control_position = gmap_lire_config('gmap_gma3_interface', 'nav_control_position', "LT");
	$corps .= '
		<p><label for="nav_control_style">'._T('gmap:choix_style_control_nav').'</label>
		<select name="nav_control_style" id="nav_control_style" class="tracked">
			<option value="none"'.(($nav_control_style === "none")?' selected="selected"':'').'>'._T('gmap:style_nav_control_none').'</option>
			<option value="auto"'.(($nav_control_style === "auto")?' selected="selected"':'').'>'._T('gmap:style_nav_control_auto').'</option>
			<option value="small"'.(($nav_control_style === "small")?' selected="selected"':'').'>'._T('gmap:style_nav_control_small').'</option>
			<option value="android"'.(($nav_control_style === "android")?' selected="selected"':'').'>'._T('gmap:style_nav_control_android').'</option>
			<option value="large"'.(($nav_control_style === "large")?' selected="selected"':'').'>'._T('gmap:style_nav_control_large').'</option>
		</select>';
	$corps .= _gmap_add_control_position_choice($nav_control_position, "nav_control_position");
	$corps .= '
		</p>';*/
	$zoom_control_style = gmap_lire_config('gmap_gma3_interface', 'zoom_control_style', "auto");
	$zoom_control_position = gmap_lire_config('gmap_gma3_interface', 'zoom_control_position', "LT");
	$corps .= '
		<p><label for="zoom_control_style">'._T('gmap:choix_style_control_zoom').'</label>
		<select name="zoom_control_style" id="zoom_control_style" class="tracked">
			<option value="none"'.(($zoom_control_style === "none")?' selected="selected"':'').'>'._T('gmap:style_zoom_control_none').'</option>
			<option value="auto"'.(($zoom_control_style === "auto")?' selected="selected"':'').'>'._T('gmap:style_zoom_control_auto').'</option>
			<option value="small"'.(($zoom_control_style === "small")?' selected="selected"':'').'>'._T('gmap:style_zoom_control_small').'</option>
			<option value="large"'.(($zoom_control_style === "large")?' selected="selected"':'').'>'._T('gmap:style_zoom_control_large').'</option>
		</select>';
	$corps .= _gmap_add_control_position_choice($zoom_control_position, "zoom_control_position");
	$corps .= '<br />';
	$pan_control_style = gmap_lire_config('gmap_gma3_interface', 'pan_control_style', "large");
	$pan_control_position = gmap_lire_config('gmap_gma3_interface', 'pan_control_position', "LT");
	$corps .= '
		<label for="pan_control_style">'._T('gmap:choix_style_control_pan').'</label>
		<select name="pan_control_style" id="pan_control_style" class="tracked">
			<option value="none"'.(($pan_control_style === "none")?' selected="selected"':'').'>'._T('gmap:style_pan_control_none').'</option>
			<option value="large"'.(($pan_control_style === "large")?' selected="selected"':'').'>'._T('gmap:style_pan_control_large').'</option>
		</select>';
	$corps .= _gmap_add_control_position_choice($pan_control_position, "pan_control_position");
	$corps .= '
		</p>';
	// Affichage de l'échelle
	$scale_control_style = gmap_lire_config('gmap_gma3_interface', 'scale_control_style', "none");
	$scale_control_position = gmap_lire_config('gmap_gma3_interface', 'scale_control_position', "BL");
	$corps .= '
		<p><label for="scale_control_style">'._T('gmap:choix_style_control_scale').'</label>
		<select name="scale_control_style" id="scale_control_style" class="tracked">
			<option value="none"'.(($scale_control_style === "none")?' selected="selected"':'').'>'._T('gmap:style_control_none').'</option>
			<option value="default"'.(($scale_control_style === "default")?' selected="selected"':'').'>'._T('gmap:style_control_default').'</option>
		</select>';
	$corps .= _gmap_add_control_position_choice($scale_control_position, "scale_control_position");
	$corps .= '
		</p>';
	// Affichage du passage en StreetView
	$streetview_control_style = gmap_lire_config('gmap_gma3_interface', 'streetview_control_style', "default");
	$streetview_control_position = gmap_lire_config('gmap_gma3_interface', 'streetview_control_position', "LT");
	$corps .= '
		<p><label for="streetview_control_style">'._T('gmap:choix_style_control_streetview').'</label>
		<select name="streetview_control_style" id="streetview_control_style" class="tracked">
			<option value="none"'.(($streetview_control_style === "none")?' selected="selected"':'').'>'._T('gmap:style_control_none').'</option>
			<option value="default"'.(($streetview_control_style === "default")?' selected="selected"':'').'>'._T('gmap:style_control_default').'</option>
		</select>';
	$corps .= _gmap_add_control_position_choice($streetview_control_position, "streetview_control_position");
	$corps .= '
		</p>';
	// Commande de rotation
	$rotate_control_style = gmap_lire_config('gmap_gma3_interface', 'rotate_control_style', "none");
	$rotate_control_position = gmap_lire_config('gmap_gma3_interface', 'rotate_control_position', "LT");
	$corps .= '
		<p><label for="rotate_control_style">'._T('gmap:choix_style_control_rotate').'</label>
		<select name="rotate_control_style" id="rotate_control_style" class="tracked">
			<option value="none"'.(($rotate_control_style === "none")?' selected="selected"':'').'>'._T('gmap:style_control_none').'</option>
			<option value="default"'.(($rotate_control_style === "default")?' selected="selected"':'').'>'._T('gmap:style_control_default').'</option>
		</select>';
	$corps .= _gmap_add_control_position_choice($rotate_control_position, "rotate_control_position");
	$corps .= '
		</p>';
	// Panneau de positionnement
	$overview_control_style = gmap_lire_config('gmap_gma3_interface', 'overview_control_style', "none");
	$corps .= '
		<p><label for="overview_control_style">'._T('gmap:choix_style_control_overview').'</label>
		<select name="overview_control_style" id="overview_control_style" class="tracked">
			<option value="none"'.(($overview_control_style === "none")?' selected="selected"':'').'>'._T('gmap:style_overview_control_none').'</option>
			<option value="open"'.(($overview_control_style === "open")?' selected="selected"':'').'>'._T('gmap:style_overview_control_open').'</option>
			<option value="close"'.(($overview_control_style === "close")?' selected="selected"':'').'>'._T('gmap:style_overview_control_close').'</option>
		</select></p>';
	// Paramètres booléens
	$allow_dblclk_zoom = gmap_lire_config('gmap_gma3_interface', 'allow_dblclk_zoom', "non");
	$allow_map_dragging = gmap_lire_config('gmap_gma3_interface', 'allow_map_dragging', "oui");
	$allow_wheel_zoom = gmap_lire_config('gmap_gma3_interface', 'allow_wheel_zoom', "non");
	$corps .= '
		<p>
			<input type="checkbox" name="allow_dblclk_zoom" id="allow_dblclk_zoom" class="tracked" value="oui"'.(($allow_dblclk_zoom==="oui")?'checked="checked"':'').' /><label for="allow_dblclk_zoom">'._T('gmap:choix_zoom_dblclk').'</label><br />
			<input type="checkbox" name="allow_map_dragging" id="allow_map_dragging" class="tracked" value="oui"'.(($allow_map_dragging==="oui")?'checked="checked"':'').' /><label for="allow_map_dragging">'._T('gmap:choix_map_dragging').'</label><br />
			<input type="checkbox" name="allow_wheel_zoom" id="allow_wheel_zoom" class="tracked" value="oui"'.(($allow_wheel_zoom==="oui")?'checked="checked"':'').' /><label for="allow_wheel_zoom">'._T('gmap:choix_zoom_wheel').'</label><br />
			<input type="checkbox" name="allow_keyboard" id="allow_keyboard" class="tracked" value="oui"'.(($allow_keyboard==="oui")?'checked="checked"':'').' /><label for="allow_keyboard">'._T('gmap:choix_keyboard').'</label>
		</p>';
	$corps .= '
	</div></div>
</fieldset>' . "\n";
	
	return $corps;
}

// Fonction qui lit les paramètres de la carte depuis l'interface ci-dessus
function _gmap_get_gma3_get_params()
{
	// Test si on peut proposer l'interface Google Earth
	$isEarth = (strlen(gmap_lire_config('gmap_api_gma3', 'key', "")) > 0) ? true : false;
	
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
	if (jQuery("#type_carte_physic:checked").val() == "oui") params.mapTypes.push("physic");';
	if ($isEarth)
		$getParams .= '
	if (jQuery("#type_carte_earth:checked").val() == "oui") params.mapTypes.push("earth");';
	$getParams .= '
	params["defaultMapType"] = jQuery("#type_carte_defaut").val();

	// Commandes et contrôles
	params["styleBackgroundCommand"] = jQuery("#types_control_style").val();
	params["positionBackgroundCommand"] = jQuery("#types_control_position").val();
	//params["styleNavigationCommand"] = jQuery("#nav_control_style").val();
	//params["positionNavigationCommand"] = jQuery("#nav_control_position").val();
	params["styleZoomCommand"] = jQuery("#zoom_control_style").val();
	params["positionZoomCommand"] = jQuery("#zoom_control_position").val();
	params["stylePanCommand"] = jQuery("#pan_control_style").val();
	params["positionPanCommand"] = jQuery("#pan_control_position").val();
	params["styleScaleControl"] = jQuery("#scale_control_style").val();
	params["positionScaleControl"] = jQuery("#scale_control_position").val();
	params["styleStreetViewCommand"] = jQuery("#streetview_control_style").val();
	params["positionStreetViewCommand"] = jQuery("#streetview_control_position").val();
	params["styleRotationCommand"] = jQuery("#rotate_control_style").val();
	params["positionRotationCommand"] = jQuery("#rotate_control_position").val();
	params["styleOverviewControl"] = jQuery("#overview_control_style").val();
	
	// Autres paramètres
	params["enableDblClkZoom"] = (jQuery("#allow_dblclk_zoom:checked").val() == "oui") ? true : false;
	params["enableMapDragging"] = (jQuery("#allow_map_dragging:checked").val() == "oui") ? true : false;
	params["enableWheelZoom"] = (jQuery("#allow_wheel_zoom:checked").val() == "oui") ? true : false;
	params["enableKeyboard"] = (jQuery("#allow_keyboard:checked").val() == "oui") ? true : false;
	
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
function mapimpl_gma3_prive_show_map_defaults_dist(&$uiElements, &$getParams)
{
	$uiElements = _gmap_get_gma3_ui_elements();
	$getParams = _gmap_get_gma3_get_params();
	return true;
}

?>
