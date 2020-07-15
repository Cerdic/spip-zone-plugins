<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le type de service GEOMETRIE.
 * Les services de GEOMETRIE sont des mécanisme internes à Nomenclatures pour
 * charger les différents ensemble de contours géographiques fournis par des services divers et sous des
 * formats variés (fichiers geoJSON, topoJSON, API REST...).
 *
 * @package SPIP\ISOCODE\SERVICES\GEOMETRIE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


$GLOBALS['isocode']['geometrie'] = array(
	'urssafregfr' => array(
		'table' => 'geoboundaries',
		'type'  => 'subdivision',
		'pays'  => 'FR',
		'basic_fields' => array(
			'code_insee' => 'code',
			'geo_shape'  => 'geometry',
			'geo_point'  => 'lat_lon',
		),
		'basic_ext_fields' => array(
			'lat',
			'lon',
		),
		'static_fields' => array(
			'service'   => '$service',
			'code_type' => 'code_insee_reg',
			'format'    => 'geojson',
			'type'      => '/type',
			'country'   => '/pays',
		),
		'unused_fields' => array(
			'lat_lon' => '',
		),
		'label_field'  => false,
		'populating'   => 'file_geojson',
		'multiple'     => false,
		'extension'    => '.json',
		'node'         => 'records',
		'basic_nodes'  => array(
			'code_insee' => 'fields/code_insee',
			'geo_shape'  => 'fields/geo_shape',
			'geo_point'  => 'fields/geo_point',
		),
	),
	'urssafdepfr' => array(
		'table' => 'geoboundaries',
		'type'  => 'subdivision',
		'pays'  => 'FR',
		'basic_fields' => array(
			'code_departement' => 'code',
			'geo_shape'        => 'geometry',
			'geo_point'        => 'lat_lon',
		),
		'basic_ext_fields' => array(
			'lat',
			'lon',
		),
		'static_fields' => array(
			'service'   => '$service',
			'code_type' => 'code_insee',
			'format'    => 'geojson',
			'type'      => '/type',
			'country'   => '/pays',
		),
		'unused_fields' => array(
			'lat_lon' => '',
		),
		'label_field'  => false,
		'populating'   => 'file_geojson',
		'multiple'     => false,
		'extension'    => '.json',
		'node'         => 'records',
		'basic_nodes'  => array(
			'code_departement' => 'fields/code_departement',
			'geo_shape'        => 'fields/geo_shape',
			'geo_point'  => 'fields/geo_point',
		),
	),
	'mapofglobe' => array(
		'table' => 'geoboundaries',
		'type'  => 'country',
		'pays'  => '',
		'basic_fields' => array(
			'iso_a2'   => 'code',
			'geometry' => 'geometry',
		),
		'static_fields' => array(
			'service'   => '$service',
			'code_type' => 'code_3166_a2',
			'format'    => 'geojson',
			'type'      => '/type',
			'country'   => '/pays',
		),
		'label_field'  => false,
		'populating'   => 'file_geojson',
		'multiple'     => true,
		'extension'    => '.json',
		'node'         => 'features',
		'basic_nodes'  => array(
			'iso_a2'   => 'properties/iso_a2',
			'geometry' => 'geometry',
		),
	),
);

// ----------------------------------------------------------------------------
// ---------- API du type de service GEOMETRIE - Actions principales ----------
// ----------------------------------------------------------------------------

function mapofglobe_completer_element($element, $config) {

	// Cette fonction permet de remplir les champs "basic_ext".

	// Et d'apporter des corrections au champs déjà compilés : attention on a encore les index source !!!
	// - serialiser le champs des géométries
	$element['geometry'] = serialize($element['geometry']);

	return $element;
}

function urssafregfr_completer_element($element, $config) {

	// Cette fonction permet de remplir les champs "basic_ext".
	// - récupérer la latitude et la longitude à partir du champ geo_point
	$element['lat'] = floatval($element['geo_point'][0]);
	$element['lon'] = floatval($element['geo_point'][1]);

	// Et d'apporter des corrections au champs déjà compilés
	// - serialiser le champs des géométries
	$element['geo_shape'] = serialize($element['geo_shape']);

	return $element;
}

function urssafdepfr_completer_element($element, $config) {

	// Cette fonction permet de remplir les champs "basic_ext".
	// - récupérer la latitude et la longitude à partir du champ geo_point
	$element['lat'] = floatval($element['geo_point'][0]);
	$element['lon'] = floatval($element['geo_point'][1]);

	// Et d'apporter des corrections au champs déjà compilés
	// - serialiser le champs des géométries
	$element['geo_shape'] = serialize($element['geo_shape']);

	return $element;
}
