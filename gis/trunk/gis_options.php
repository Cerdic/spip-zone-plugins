<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// pour compat cf https://zone.spip.org/trac/spip-zone/changeset/79911/
define('_DIR_LIB_GIS', find_in_path('lib/leaflet/'));

$config = @unserialize($GLOBALS['meta']['gis']);
$api_key_bing = isset($config['api_key_bing']) ? trim($config['api_key_bing']) : '';

$gis_layers = array (
	'openstreetmap_mapnik' => array(
		'nom' => 'OpenStreetMap',
		'layer' => 'L.tileLayer.provider("OpenStreetMap")'
	),
	'openstreetmap_de' => array(
		'nom' => 'OpenStreetMap DE',
		'layer' => 'L.tileLayer.provider("OpenStreetMap.DE")'
	),
	'openstreetmap_fr' => array(
		'nom' => 'OpenStreetMap FR',
		'layer' => 'L.tileLayer.provider("OpenStreetMap.France")'
	),
	'openstreetmap_hot' => array(
		'nom' => 'OpenStreetMap H.O.T.',
		'layer' => 'L.tileLayer.provider("OpenStreetMap.HOT")'
	),
	'opentopomap' => array(
		'nom' => 'OpenTopoMap',
		'layer' => 'L.tileLayer.provider("OpenTopoMap")'
	),
	'hydda' => array(
		'nom' => 'Hydda',
		'layer' => 'L.tileLayer.provider("Hydda")'
	),
	'hydda_base' => array(
		'nom' => 'Hydda Base',
		'layer' => 'L.tileLayer.provider("Hydda.Base")'
	),
	'wikimedia' => array(
		'nom' => 'Wikimedia',
		'layer' => 'L.tileLayer.provider("Wikimedia")'
	),
	'cartodb_positron' => array(
		'nom' => 'CartoDB Positron',
		'layer' => 'L.tileLayer.provider("CartoDB.Positron")'
	),
	'cartodb_positron_base' => array(
		'nom' => 'CartoDB Positron Base',
		'layer' => 'L.tileLayer.provider("CartoDB.PositronNoLabels")'
	),
	'cartodb_darkmatter' => array(
		'nom' => 'CartoDB DarkMatter',
		'layer' => 'L.tileLayer.provider("CartoDB.DarkMatter")'
	),
	'cartodb_darkmatter_base' => array(
		'nom' => 'CartoDB DarkMatter Base',
		'layer' => 'L.tileLayer.provider("CartoDB.DarkMatterNoLabels")'
	),
	'cartodb_voyager' => array(
		'nom' => 'CartoDB Voyager',
		'layer' => 'L.tileLayer.provider("CartoDB.Voyager")'
	),
	'cartodb_voyager_base' => array(
		'nom' => 'CartoDB Voyager Base',
		'layer' => 'L.tileLayer.provider("CartoDB.VoyagerNoLabels")'
	),
	'stamen_toner' => array(
		'nom' => 'Stamen Toner',
		'layer' => 'L.tileLayer.provider("Stamen.Toner")'
	),
	'stamen_tonerlite' => array(
		'nom' => 'Stamen Toner Lite',
		'layer' => 'L.tileLayer.provider("Stamen.TonerLite")'
	),
	'stamen_terrain' => array(
		'nom' => 'Stamen Terrain',
		'layer' => 'L.tileLayer.provider("Stamen.Terrain")'
	),
	'stamen_watercolor' => array(
		'nom' => 'Stamen Watercolor',
		'layer' => 'L.tileLayer.provider("Stamen.Watercolor")'
	),
	'esri_worldstreetmap' => array(
		'nom' => 'Esri WorldStreetMap',
		'layer' => 'L.tileLayer.provider("Esri.WorldStreetMap")'
	),
	'esri_delorme' => array(
		'nom' => 'Esri DeLorme',
		'layer' => 'L.tileLayer.provider("Esri.DeLorme")'
	),
	'esri_worldtopomap' => array(
		'nom' => 'Esri WorldTopoMap',
		'layer' => 'L.tileLayer.provider("Esri.WorldTopoMap")'
	),
	'esri_worldimagery' => array(
		'nom' => 'Esri WorldImagery',
		'layer' => 'L.tileLayer.provider("Esri.WorldImagery")'
	),
	'esri_worldterrain' => array(
		'nom' => 'Esri WorldTerrain',
		'layer' => 'L.tileLayer.provider("Esri.WorldTerrain")'
	),
	'esri_worldshadedrelief' => array(
		'nom' => 'Esri WorldShadedRelief',
		'layer' => 'L.tileLayer.provider("Esri.WorldShadedRelief")'
	),
	'esri_worldphysical' => array(
		'nom' => 'Esri WorldPhysical',
		'layer' => 'L.tileLayer.provider("Esri.WorldPhysical")'
	),
	'esri_oceanbasemap' => array(
		'nom' => 'Esri OceanBasemap',
		'layer' => 'L.tileLayer.provider("Esri.OceanBasemap")'
	),
	'esri_natgeoworldmap' => array(
		'nom' => 'Esri NatGeoWorldMap',
		'layer' => 'L.tileLayer.provider("Esri.NatGeoWorldMap")'
	),
	'esri_worldgraycanvas' => array(
		'nom' => 'Esri WorldGrayCanvas',
		'layer' => 'L.tileLayer.provider("Esri.WorldGrayCanvas")'
	),
	'bing_aerial' => array(
		'nom' => 'Bing Aerial',
		'layer' => 'L.BingLayer("'.$api_key_bing.'")'
	),
	'google_roadmap' => array(
		'nom' => 'Google Roadmap',
		'layer' => 'L.gridLayer.googleMutant({type:"roadmap"})'
	),
	'google_satellite' => array(
		'nom' => 'Google Satellite',
		'layer' => 'L.gridLayer.googleMutant({type:"satellite"})'
	),
	'google_terrain' => array(
		'nom' => 'Google Terrain',
		'layer' => 'L.gridLayer.googleMutant({type:"terrain"})'
	)
);

if (isset($GLOBALS['gis_layers']) and is_array($GLOBALS['gis_layers'])) {
	$GLOBALS['gis_layers'] = array_merge($gis_layers, $GLOBALS['gis_layers']);
} else {
	$GLOBALS['gis_layers'] = $gis_layers;
}
