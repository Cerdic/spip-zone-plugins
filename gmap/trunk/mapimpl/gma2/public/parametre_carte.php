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
function mapimpl_gma2_public_parametre_carte_dist($viewport, $params)
{
	// Construction d'un tableau des vues autorisées
	$types = array();
	if (gmap_lire_config('gmap_gma2_interface', 'type_carte_plan', "oui") === "oui")
		$types[] = '"plan"';
	if (gmap_lire_config('gmap_gma2_interface', 'type_carte_satellite', "oui") === "oui")
		$types[] = '"satellite"';
	if (gmap_lire_config('gmap_gma2_interface', 'type_carte_mixte', "oui") === "oui")
		$types[] = '"mixte"';
	if (gmap_lire_config('gmap_gma2_interface', 'type_carte_physic', "oui") === "oui")
		$types[] = '"physic"';
	if (gmap_lire_config('gmap_gma2_interface', 'type_carte_earth', "oui") === "oui")
		$types[] = '"earth"';
	if (count($types) == 0)
		$types[] = '"mixte"';
		
	// Code javascript correspondant
	$code = '{ // Paramètres Google Maps V2
		viewLatitude: '.(isset($params['latitude']) ? $params['latitude'] : $viewport['latitude']).',
		viewLongitude: '.(isset($params['longitude']) ? $params['longitude'] : $viewport['longitude']).',
		viewZoom: '.(isset($params['zoom']) ? $params['zoom'] : $viewport['zoom']).',
		mapTypes: ['.implode(', ', $types).'],
		defaultMapType: "'.(isset($params['fond']) ? $params['fond'] : gmap_lire_config('gmap_gma2_interface', 'type_defaut', "mixte")).'",
		styleBackgroundCommand: "'.(isset($params['ctrl_fond']) ? $params['ctrl_fond'] : gmap_lire_config('gmap_gma2_interface', 'types_control_style', "menu")).'",
		styleNavigationCommand: "'.(isset($params['ctrl_nav']) ? $params['ctrl_nav'] : gmap_lire_config('gmap_gma2_interface', 'nav_control_style', "3D")).'",
		enableDblClkZoom: '.(((isset($params['option_dblclk_zoom']) ? $params['option_dblclk_zoom'] : gmap_lire_config('gmap_gma2_interface', 'allow_dblclk_zoom', "non")) === "oui") ? 'true' : 'false').',
		enableContinuousZoom: '.(((isset($params['option_soft_zoom']) ? $params['option_soft_zoom'] : gmap_lire_config('gmap_gma2_interface', 'allow_continuous_zoom', "non")) === "oui") ? 'true' : 'false').',
		enableWheelZoom: '.(((isset($params['option_wheel_zoom']) ? $params['option_wheel_zoom'] : gmap_lire_config('gmap_gma2_interface', 'allow_wheel_zoom', "non")) === "oui") ? 'true' : 'false').',
		infoWidthPercent: '.gmap_lire_config('gmap_gma2_interface', 'info_width_percent', "65").',
		infoWidthAbsolute: '.gmap_lire_config('gmap_gma2_interface', 'info_width_absolute', "300").',
		mergeInfoWindows: '.((gmap_lire_config('gmap_gma2_interface', 'merge_infos', "non") === "oui") ? 'true' : 'false').'
	}';
	
	return $code;
}

?>