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
function mapimpl_mxn_public_parametre_carte_dist($viewport, $params)
{
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
			"'.$name.'": '.(($value === 'oui') ? 'true' : 'false').',';
	$code .= '
			},';
	$code .= '	
		map_type: "'.(isset($params['fond']) ? $params['fond'] : gmap_lire_config('gmap_mxn_interface', 'type_defaut', "mixte")).'",
		ctrl_map_type: '.(isset($params['ctrl_fond']) ? (($params['ctrl_fond'] == 'oui') ? 'true' : 'false') : ((gmap_lire_config('gmap_mxn_interface', 'types_control', "oui") == 'oui') ? 'true' : 'false')).',
		ctrl_pan: '.(isset($params['ctrl_pan']) ? (($params['ctrl_pan'] == 'oui') ? 'true' : 'false') : ((gmap_lire_config('gmap_mxn_interface', 'pan_control', "oui") == 'oui') ? 'true' : 'false')).',
		ctrl_zoom: "'.(isset($params['ctrl_zoom']) ? $params['ctrl_zoom'] : gmap_lire_config('gmap_mxn_interface', 'zoom_control', "small")).'",
		ctrl_scale: '.(isset($params['ctrl_scale']) ? (($params['ctrl_scale'] == 'oui') ? 'true' : 'false') : ((gmap_lire_config('gmap_mxn_interface', 'scale_control', "oui") == 'oui') ? 'true' : 'false')).',
		ctrl_overview: '.(isset($params['ctrl_overview']) ? (($params['ctrl_overview'] == 'oui') ? 'true' : 'false') : ((gmap_lire_config('gmap_mxn_interface', 'overview_control', "oui") == 'oui') ? 'true' : 'false')).',
	}';
	
	return $code;
}

?>