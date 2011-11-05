<?php
/*
 * Extension Mapstraction pour GMap
 *
 * Auteur : Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS['allowed_providers'] = array(
	'cloudmade' => array(	'desc' => _T('gmapmxn:api_provider_cloudmade'), 	'key'=>'oui', 	'geocoder'=>'non',	'kml'=>'non', 'maptypes'=>'non', 'auto_updt_controls'=>'non', 'drag_markers'=>'non', 'shadow_icon'=>'oui', 'marker_click_handler'=>'non', 'ctrl_zoom'=>'oui', 'ctrl_pan'=>'non', 'ctrl_scale'=>'oui', 'ctrl_overview'=>'non', 'ctrl_maptypes'=>'oui'),
	'google' => array(		'desc' => _T('gmapmxn:api_provider_google'), 		'key'=>'oui', 	'geocoder'=>'oui',	'kml'=>'oui', 'maptypes'=>'oui', 'auto_updt_controls'=>'oui', 'drag_markers'=>'non', 'shadow_icon'=>'oui', 'marker_click_handler'=>'oui', 'ctrl_zoom'=>'oui', 'ctrl_pan'=>'non', 'ctrl_scale'=>'oui', 'ctrl_overview'=>'oui', 'ctrl_maptypes'=>'oui'), // Il est possible de déplacer les marqueurs, mais pas d'évènement drop/dragend
	'googlev3' => array(	'desc' => _T('gmapmxn:api_provider_googlev3'), 		'key'=>'non', 	'geocoder'=>'oui',	'kml'=>'oui', 'maptypes'=>'oui', 'auto_updt_controls'=>'non', 'drag_markers'=>'non', 'shadow_icon'=>'oui', 'marker_click_handler'=>'oui', 'ctrl_zoom'=>'oui', 'ctrl_pan'=>'non', 'ctrl_scale'=>'oui', 'ctrl_overview'=>'non', 'ctrl_maptypes'=>'oui'), // Il est possible de déplacer les marqueurs, mais pas d'évènement drop/dragend
	'microsoft' => array(	'desc' => _T('gmapmxn:api_provider_microsoft'), 	'key'=>'non', 	'geocoder'=>'non',	'kml'=>'oui', 'maptypes'=>'oui', 'auto_updt_controls'=>'non', 'drag_markers'=>'non', 'shadow_icon'=>'non', 'marker_click_handler'=>'oui', 'ctrl_zoom'=>'oui', 'ctrl_pan'=>'non', 'ctrl_scale'=>'non', 'ctrl_overview'=>'non', 'ctrl_maptypes'=>'non'), // Il est possible de déplacer les marqueurs, mais pas d'évènement drop/dragend. Normalement on pourrait changer dynamiquement les contrôles, mais quand on passe en mode "non" du zoom, on n'en revient jamais, donc désactivé. Seule la commande de zoom semble fonctionner alors que le code de l'implémentation Mapstraction semble gérer le pan.
	'openlayers' => array(	'desc' => _T('gmapmxn:api_provider_openlayers'), 	'key'=>'non', 	'geocoder'=>'non',	'kml'=>'oui', 'maptypes'=>'non', 'auto_updt_controls'=>'oui', 'drag_markers'=>'non', 'shadow_icon'=>'non', 'marker_click_handler'=>'non', 'ctrl_zoom'=>'oui', 'ctrl_pan'=>'oui', 'ctrl_scale'=>'non', 'ctrl_overview'=>'oui', 'ctrl_maptypes'=>'oui'),
	'ovi' => array(			'desc' => _T('gmapmxn:api_provider_ovi'), 			'key'=>'non', 	'geocoder'=>'non',	'kml'=>'non', 'maptypes'=>'oui', 'auto_updt_controls'=>'non', 'drag_markers'=>'non', 'shadow_icon'=>'non', 'marker_click_handler'=>'non', 'ctrl_zoom'=>'oui', 'ctrl_pan'=>'non', 'ctrl_scale'=>'oui', 'ctrl_overview'=>'oui', 'ctrl_maptypes'=>'oui'), // Il est possible de déplacer les marqueurs, mais pas d'évènement drop/dragend. Normalement il devrait y avoir une commande pan mais elle ne fonctionne pas
	'yahoo' => array(		'desc' => _T('gmapmxn:api_provider_yahoo'),			'key'=>'oui', 	'geocoder'=>'non',	'kml'=>'oui', 'maptypes'=>'oui', 'auto_updt_controls'=>'non', 'drag_markers'=>'non', 'shadow_icon'=>'non', 'marker_click_handler'=>'oui', 'ctrl_zoom'=>'oui', 'ctrl_pan'=>'oui', 'ctrl_scale'=>'non', 'ctrl_overview'=>'non', 'ctrl_maptypes'=>'non'), // On devrait pouvoir changer dynamiquement les marqueurs, mais ça ne marche pas.
	'yandex' => array(		'desc' => _T('gmapmxn:api_provider_yandex'), 		'key'=>'oui', 	'geocoder'=>'oui',	'kml'=>'non', 'maptypes'=>'oui', 'auto_updt_controls'=>'non', 'drag_markers'=>'non', 'shadow_icon'=>'non', 'marker_click_handler'=>'non', 'ctrl_zoom'=>'oui', 'ctrl_pan'=>'oui', 'ctrl_scale'=>'non', 'ctrl_overview'=>'non', 'ctrl_maptypes'=>'non'),
	);
	
include_spip('inc/gmap_config_utils');

function gmapmxn_getProvider()
{
	return gmap_lire_config('gmap_api_mxn', 'provider', "openlayers");
}

function gmapmxn_getProviderCaps($provider)
{
	return $GLOBALS['allowed_providers'][strtolower($provider)];
}

function gmapmxn_getCaps()
{
	return $GLOBALS['allowed_providers'][gmapmxn_getProvider()];
}

function gmapmxn_hasCapability($capability)
{
	$caps = gmapmxn_getCaps();
	return ($caps[strtolower($capability)] === 'oui') ? true : false;
}

?>