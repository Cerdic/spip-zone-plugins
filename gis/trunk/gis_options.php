<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// pour compat cf http://zone.spip.org/trac/spip-zone/changeset/79911/
define('_DIR_LIB_GIS',find_in_path('lib/leaflet/'));

$GLOBALS['logo_libelles']['id_gis'] = _T('gis:libelle_logo_gis');

$config = @unserialize($GLOBALS['meta']['gis']);

$GLOBALS['gis_layers'] = array (
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
	'openstreetmap_hot' => array(
		'nom' => 'OpenStreetMap H.O.T.',
		'layer' => 'L.tileLayer.provider("OpenStreetMap.HOT")'
	),
	'google_roadmap' => array(
		'nom' => 'Google Roadmap',
		'layer' => 'L.Google("ROADMAP")'
	),
	'google_satellite' => array(
		'nom' => 'Google Satelitte',
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
	'mapquestopen_osm' => array(
		'nom' => 'Mapquest Open',
		'layer' => 'L.tileLayer.provider("MapQuestOpen.OSM")'
	),
	'mapquestopen_aerial' => array(
		'nom' => 'Mapquest Open Aerial',
		'layer' => 'L.tileLayer.provider("MapQuestOpen.Aerial")'
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
	'acetate' => array(
		'nom' => 'Acetate',
		'layer' => 'L.tileLayer.provider("Acetate.all")'
	)
);

?>