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

// Paramétrage de la carte selon ce qui est défini dans la partie privée
function mapimpl_mxn_public_parametre_carte_dist($viewport, $params, $profile = 'interface')
{
	// La clef à laquelle se trouvent les configurations dépend du profile et de l'api
	if (!isset($profile))
		$profile = 'interface';
	$apiConfigKey = 'gmap_mxn_'.$profile;
	
	$code = '
	{ // Paramètres Mapstraction pour '.gmapmxn_getProvider().'
		viewLatitude: '.(isset($params['latitude']) ? $params['latitude'] : $viewport['latitude']).',
		viewLongitude: '.(isset($params['longitude']) ? $params['longitude'] : $viewport['longitude']).',
		viewZoom: '.(isset($params['zoom']) ? $params['zoom'] : $viewport['zoom']).',
		provider: "'.gmapmxn_getProvider().'",';
	$caps = gmapmxn_getCaps();
	$code .= '
		caps: {';
	foreach ($caps as $name => $value)
	$code .= '
			'.$name.': '.(($value === 'oui') ? 'true' : 'false').',';
	$code .= '
			},';
	$code .= '	
		map_type: "'.(isset($params['fond']) ? $params['fond'] : gmap_lire_config($apiConfigKey, 'type_defaut', "mixte")).'",
		ctrl_map_type: '.(isset($params['ctrl_fond']) ? (($params['ctrl_fond'] == 'oui') ? 'true' : 'false') : ((gmap_lire_config($apiConfigKey, 'types_control', "oui") == 'oui') ? 'true' : 'false')).',
		ctrl_pan: '.(isset($params['ctrl_pan']) ? (($params['ctrl_pan'] == 'oui') ? 'true' : 'false') : ((gmap_lire_config($apiConfigKey, 'pan_control', "oui") == 'oui') ? 'true' : 'false')).',
		ctrl_zoom: "'.(isset($params['ctrl_zoom']) ? $params['ctrl_zoom'] : gmap_lire_config($apiConfigKey, 'zoom_control', "small")).'",
		ctrl_scale: '.(isset($params['ctrl_scale']) ? (($params['ctrl_scale'] == 'oui') ? 'true' : 'false') : ((gmap_lire_config($apiConfigKey, 'scale_control', "oui") == 'oui') ? 'true' : 'false')).',
		ctrl_overview: '.(isset($params['ctrl_overview']) ? (($params['ctrl_overview'] == 'oui') ? 'true' : 'false') : ((gmap_lire_config($apiConfigKey, 'overview_control', "oui") == 'oui') ? 'true' : 'false')).',
	}';
	
	return $code;
}

?>