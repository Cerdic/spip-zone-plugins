<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

define('_DIR_LIB_GIS','lib/leaflet-gis-4.1.0/');

$GLOBALS['logo_libelles']['id_gis'] = _T('gis:libelle_logo_gis');

$config = @unserialize($GLOBALS['meta']['gis']);

$GLOBALS['gis_layers'] = array (
	'openstreetmap_mapnik' => array(
		'nom' => 'OpenStreetMap',
		'layer' => 'L.TileLayer.OpenStreetMap.Mapnik()'
	),
	'openstreetmap_blackandwhite' => array(
		'nom' => 'OpenStreetMap Black and White',
		'layer' => 'L.TileLayer.OpenStreetMap.BlackAndWhite()'
	),
	'openstreetmap_de' => array(
		'nom' => 'OpenStreetMap DE',
		'layer' => 'L.TileLayer.OpenStreetMap.DE()'
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
		'layer' => 'L.TileLayer.Thunderforest.OpenCycleMap()',
	),
	'thunderforest_transport' => array(
		'nom' => 'Thunderforest Transport',
		'layer' => 'L.TileLayer.Thunderforest.Transport()'
	),
	'thunderforest_landscape' => array(
		'nom' => 'Thunderforest Landscape',
		'layer' => 'L.TileLayer.Thunderforest.Landscape()'
	),
	'mapquestopen_osm' => array(
		'nom' => 'Mapquest Open',
		'layer' => 'L.TileLayer.MapQuestOpen.OSM()'
	),
	'mapquestopen_aerial' => array(
		'nom' => 'Mapquest Open Aerial',
		'layer' => 'L.TileLayer.MapQuestOpen.Aerial()'
	),
	'mapbox_simple' => array(
		'nom' => 'MapBox Simple',
		'layer' => 'L.TileLayer.MapBox.Simple()',
	),
	'mapbox_streets' => array(
		'nom' => 'MapBox Streets',
		'layer' => 'L.TileLayer.MapBox.Streets()'
	),
	'mapbox_light' => array(
		'nom' => 'MapBox Light',
		'layer' => 'L.TileLayer.MapBox.Light()'
	),
	'mapbox_lacquer' => array(
		'nom' => 'MapBox Lacquer',
		'layer' => 'L.TileLayer.MapBox.Lacquer()'
	),
	'mapbox_warden' => array(
		'nom' => 'MapBox Warden',
		'layer' => 'L.TileLayer.MapBox.Warden()'
	),
	'stamen_toner' => array(
		'nom' => 'Stamen Toner',
		'layer' => 'L.TileLayer.Stamen.Toner()'
	),
	'stamen_terrain' => array(
		'nom' => 'Stamen Terrain',
		'layer' => 'L.TileLayer.Stamen.Terrain()'
	),
	'stamen_watercolor' => array(
		'nom' => 'Stamen Watercolor',
		'layer' => 'L.TileLayer.Stamen.Watercolor()'
	),
	'esri_worldstreetmap' => array(
		'nom' => 'Esri WorldStreetMap',
		'layer' => 'L.TileLayer.Esri.WorldStreetMap()'
	),
	'esri_delorme' => array(
		'nom' => 'Esri DeLorme',
		'layer' => 'L.TileLayer.Esri.DeLorme()'
	),
	'esri_worldtopomap' => array(
		'nom' => 'Esri WorldTopoMap',
		'layer' => 'L.TileLayer.Esri.WorldTopoMap()'
	),
	'esri_worldimagery' => array(
		'nom' => 'Esri WorldImagery',
		'layer' => 'L.TileLayer.Esri.WorldImagery()'
	),
	'esri_oceanbasemap' => array(
		'nom' => 'Esri OceanBasemap',
		'layer' => 'L.TileLayer.Esri.OceanBasemap()'
	),
	'esri_natgeoworldmap' => array(
		'nom' => 'Esri NatGeoWorldMap',
		'layer' => 'L.TileLayer.Esri.NatGeoWorldMap()'
	),
);

?>