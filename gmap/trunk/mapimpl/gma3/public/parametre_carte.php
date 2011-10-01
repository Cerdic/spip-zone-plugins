<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Paramétrage de la carte dans l'espace public
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

// Enregistrement des paramètres passés dans la requête
function mapimpl_gma3_public_parametre_carte_dist($viewport, $params)
{
	// Construction d'un tableau des vues autorisées
	$types = array();
	if (gmap_lire_config('gmap_gma3_interface', 'type_carte_plan', "oui") === "oui")
		$types[] = '"plan"';
	if (gmap_lire_config('gmap_gma3_interface', 'type_carte_satellite', "oui") === "oui")
		$types[] = '"satellite"';
	if (gmap_lire_config('gmap_gma3_interface', 'type_carte_mixte', "oui") === "oui")
		$types[] = '"mixte"';
	if (gmap_lire_config('gmap_gma3_interface', 'type_carte_physic', "oui") === "oui")
		$types[] = '"physic"';
	$key = gmap_lire_config('gmap_api_gma3', 'key', "");
	if (strlen($key) > 0)
	{
		if (gmap_lire_config('gmap_gma3_interface', 'type_carte_earth', "oui") === "oui")
			$types[] = '"earth"';
	}
	if (count($types) == 0)
		$types[] = '"mixte"';
		
	// Code javascript correspondant
	$code = '{ // Paramètres Google Maps V3
		viewLatitude: '.(isset($params['latitude']) ? $params['latitude'] : $viewport['latitude']).',
		viewLongitude: '.(isset($params['longitude']) ? $params['longitude'] : $viewport['longitude']).',
		viewZoom: '.(isset($params['zoom']) ? $params['zoom'] : $viewport['zoom']).',
		mapTypes: ['.implode(', ', $types).'],
		defaultMapType: "'.(isset($params['fond']) ? $params['fond'] : gmap_lire_config('gmap_gma3_interface', 'type_defaut', "mixte")).'",
		styleBackgroundCommand: "'.(isset($params['ctrl_fond']) ? $params['ctrl_fond'] : gmap_lire_config('gmap_gma3_interface', 'types_control_style', "menu")).'",
		positionBackgroundCommand: "'.gmap_lire_config('gmap_gma3_interface', 'types_control_position', "TR").'",
		styleZoomCommand: "'.(isset($params['ctrl_zoom']) ? $params['ctrl_zoom'] : gmap_lire_config('gmap_gma3_interface', 'zoom_control_style', "auto")).'",
		positionZoomCommand: "'.gmap_lire_config('gmap_gma3_interface', 'zoom_control_position', "LT").'",
		stylePanCommand: "'.(isset($params['ctrl_pan']) ? $params['ctrl_pan'] : gmap_lire_config('gmap_gma3_interface', 'pan_control_style', "large")).'",
		positionPanCommand: "'.gmap_lire_config('gmap_gma3_interface', 'pan_control_position', "LT").'",
		styleScaleControl: "'.(isset($params['ctrl_scale']) ? $params['ctrl_scale'] : gmap_lire_config('gmap_gma3_interface', 'scale_control_style', "none")).'",
		positionScaleControl: "'.gmap_lire_config('gmap_gma3_interface', 'scale_control_position', "BL").'",
		styleStreetViewCommand: "'.(isset($params['ctrl_street']) ? $params['ctrl_street'] : gmap_lire_config('gmap_gma3_interface', 'streetview_control_style', "default")).'",
		positionStreetViewCommand: "'.gmap_lire_config('gmap_gma3_interface', 'streetview_control_position', "LT").'",
		styleRotationCommand: "'.(isset($params['ctrl_rotate']) ? $params['ctrl_rotate'] : gmap_lire_config('gmap_gma3_interface', 'rotate_control_style', "none")).'",
		positionRotationCommand: "'.gmap_lire_config('gmap_gma3_interface', 'rotate_control_position', "LT").'",
		styleOverviewControl: "'.(isset($params['ctrl_overview']) ? $params['ctrl_overview'] : gmap_lire_config('gmap_gma3_interface', 'overview_control_style', "none")).'",
		enableDblClkZoom: '.(((isset($params['option_dblclk_zoom']) ? $params['option_dblclk_zoom'] : gmap_lire_config('gmap_gma3_interface', 'allow_dblclk_zoom', "non")) === "oui") ? 'true' : 'false').',
		enableMapDragging: '.(((isset($params['option_drag']) ? $params['option_drag'] : gmap_lire_config('gmap_gma3_interface', 'allow_map_dragging', "oui")) === "oui") ? 'true' : 'false').',
		enableWheelZoom: '.(((isset($params['option_wheel_zoom']) ? $params['option_wheel_zoom'] : gmap_lire_config('gmap_gma3_interface', 'allow_wheel_zoom', "non")) === "oui") ? 'true' : 'false').',
		enableKeyboard: '.(((isset($params['option_keyboard']) ? $params['option_keyboard'] : gmap_lire_config('gmap_gma3_interface', 'allow_keyboard', "non")) === "oui") ? 'true' : 'false').',
		infoWidthPercent: '.gmap_lire_config('gmap_gma3_interface', 'info_width_percent', "65").',
		infoWidthAbsolute: '.gmap_lire_config('gmap_gma3_interface', 'info_width_absolute', "300").',
		mergeInfoWindows: '.((gmap_lire_config('gmap_gma3_interface', 'merge_infos', "non") === "oui") ? 'true' : 'false').'
	}';
	
	return $code;
}

?>