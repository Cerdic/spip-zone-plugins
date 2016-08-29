<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// pour compat cf http://zone.spip.org/trac/spip-zone/changeset/79911/
define('_DIR_LIB_GIS', find_in_path('lib/leaflet/'));

$GLOBALS['logo_libelles']['id_gis'] = _T('gis:libelle_logo_gis');

$config = @unserialize($GLOBALS['meta']['gis']);

$gis_layers = array (
	'openstreetmap_mapnik' => array(
		'nom' => 'OpenStreetMap',
		'layer' => 'L.tileLayer.provider("OpenStreetMap")'
	),
	'openstreetmap_blackandwhite' => array(
		'nom' => 'OpenStreetMap Black and White',
		'layer' => 'L.tileLayer.provider("OpenStreetMap.BlackAndWhite")'
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
	'google_roadmap' => array(
		'nom' => 'Google Roadmap',
		'layer' => 'L.Google("ROADMAP")'
	),
	'google_satellite' => array(
		'nom' => 'Google Satellite',
		'layer' => 'L.Google("SATELLITE")'
	),
	'google_terrain' => array(
		'nom' => 'Google Terrain',
		'layer' => 'L.Google("TERRAIN")'
	),
	'bing_aerial' => array(
		'nom' => 'Bing Aerial',
		'layer' => 'L.BingLayer("'.$config['api_key_bing'].'")'
	),
	'thunderforest_opencyclemap' => array(
		'nom' => 'Thunderforest OpenCycleMap',
		'layer' => 'L.tileLayer.provider("Thunderforest.OpenCycleMap")'
	),
	'thunderforest_transport' => array(
		'nom' => 'Thunderforest Transport',
		'layer' => 'L.tileLayer.provider("Thunderforest.Transport")'
	),
	'thunderforest_landscape' => array(
		'nom' => 'Thunderforest Landscape',
		'layer' => 'L.tileLayer.provider("Thunderforest.Landscape")'
	),
	'thunderforest_outdoors' => array(
		'nom' => 'Thunderforest Outdoors',
		'layer' => 'L.tileLayer.provider("Thunderforest.Outdoors")'
	),
	'thunderforest_spinalmap' => array(
		'nom' => 'Thunderforest SpinalMap',
		'layer' => 'L.tileLayer.provider("Thunderforest.SpinalMap")'
	),
	'thunderforest_pioneer' => array(
		'nom' => 'Thunderforest Pioneer',
		'layer' => 'L.tileLayer.provider("Thunderforest.Pioneer")'
	),
	'opentopomap' => array(
		'nom' => 'OpenTopoMap',
		'layer' => 'L.tileLayer.provider("OpenTopoMap")'
	),
	'openmapsurfer' => array(
		'nom' => 'OpenMapSurfer',
		'layer' => 'L.tileLayer.provider("OpenMapSurfer")'
	),
	'openmapsurfer_grayscale' => array(
		'nom' => 'OpenMapSurfer Grayscale',
		'layer' => 'L.tileLayer.provider("OpenMapSurfer.Grayscale")'
	),
	'hydda' => array(
		'nom' => 'Hydda',
		'layer' => 'L.tileLayer.provider("Hydda")'
	),
	'hydda_base' => array(
		'nom' => 'Hydda Base',
		'layer' => 'L.tileLayer.provider("Hydda.Base")'
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
	'cartodb_positron' => array(
		'nom' => 'CartoDB Positron',
		'layer' => substr($GLOBALS['meta']['adresse_site'], 0, 5) == 'https' ? 'L.tileLayer("https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png")' : 'L.tileLayer.provider("CartoDB.Positron")'
	),
	'cartodb_positron_base' => array(
		'nom' => 'CartoDB Positron Base',
		'layer' => substr($GLOBALS['meta']['adresse_site'], 0, 5) == 'https' ? 'L.tileLayer("https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_nolabels/{z}/{x}/{y}.png")' : 'L.tileLayer.provider("CartoDB.PositronNoLabels")'
	),
	'cartodb_darkmatter' => array(
		'nom' => 'CartoDB DarkMatter',
		'layer' => substr($GLOBALS['meta']['adresse_site'], 0, 5) == 'https' ? 'L.tileLayer("https://cartodb-basemaps-{s}.global.ssl.fastly.net/dark_all/{z}/{x}/{y}.png")' : 'L.tileLayer.provider("CartoDB.DarkMatter")'
	),
	'cartodb_darkmatter_base' => array(
		'nom' => 'CartoDB DarkMatter Base',
		'layer' => substr($GLOBALS['meta']['adresse_site'], 0, 5) == 'https' ? 'L.tileLayer("https://cartodb-basemaps-{s}.global.ssl.fastly.net/dark_nolabels/{z}/{x}/{y}.png")' : 'L.tileLayer.provider("CartoDB.DarkMatterNoLabels")'
	)
);

if (isset($GLOBALS['gis_layers']) and is_array($GLOBALS['gis_layers'])) {
	$GLOBALS['gis_layers'] = array_merge($gis_layers, $GLOBALS['gis_layers']);
} else {
	$GLOBALS['gis_layers'] = $gis_layers;
}
