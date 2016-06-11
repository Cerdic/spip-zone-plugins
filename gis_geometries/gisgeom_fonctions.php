<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Balise GEOMETRY pour afficher le champ geo de la table spip_gis au format WKT
 *
 * @param $p
 * @return mixed
 */
function balise_geometry_dist($p) {
	$p->code = '$Pile[$SP][\'geometry\']';
	return $p;
}

/**
 * Balise GEOMETRY_STYLES pour afficher la représentation JSON des styles
 *
 * @param $p
 * @return mixed
 */
function balise_geometry_styles_dist($p) {
	$p->code = '$Pile[$SP][\'geometry_styles\']';
	return $p;
}

/**
 * Filtre wkt_to_json converti une chaine au format WKT en GeoJSON
 *
 * @param string $wkt
 * @return string
 */
function wkt_to_json($wkt) {
	if (!$wkt) {
		return false;
	}
	// include_spip cherche les fichier .php, on utilise find_in_path avec l'option $include à true
	find_in_path(_DIR_LIB_GEOPHP.'geoPHP.inc', '', true);
	$geometry = geoPHP::load($wkt, 'wkt');
	return $geometry->out('json');
}

/**
 * Filtre json_to_wkt converti une chaine au format GeoJSON en WKT
 *
 * @param string $json
 * @return string
 */
function json_to_wkt($json) {
	if (!$json) {
		return false;
	}
	find_in_path(_DIR_LIB_GEOPHP.'geoPHP.inc', '', true);
	$geometry = geoPHP::load($json, 'json');
	return $geometry->out('wkt');
}

/**
 * Filtre wkt_to_kml converti une chaine au format WKT en KML
 *
 * @param string $wkt
 * @return string
 */
function wkt_to_kml($wkt) {
	if (!$wkt) {
		return false;
	}
	// include_spip cherche les fichier .php, on utilise find_in_path avec l'option $include à true
	find_in_path(_DIR_LIB_GEOPHP.'geoPHP.inc', '', true);
	$geometry = geoPHP::load($wkt, 'wkt');
	return $geometry->out('kml');
}

/**
 * Filtre wkt_to_gpx converti une chaine au format WKT en GPX
 *
 * @param string $wkt
 * @return string
 */
function wkt_to_gpx($wkt) {
	if (!$wkt) {
		return false;
	}
	// include_spip cherche les fichier .php, on utilise find_in_path avec l'option $include à true
	find_in_path(_DIR_LIB_GEOPHP.'geoPHP.inc', '', true);
	$geometry = geoPHP::load($wkt, 'wkt');
	return $geometry->out('gpx');
}

/**
 * Filtre geometry_styles_to_json converti une chaine de valeurs séparées par des virugles au format JSON
 *
 * @param string $geometry_styles
 * @return string
 */
function geometry_styles_to_json($geometry_styles) {
	$values = explode(',', $geometry_styles);
	if (count(array_filter($values)) < 1) {
		return false;
	}
	$styles = array();
	$keys = array('color', 'weight', 'opacity', 'fillColor', 'fillOpacity');
	foreach ($keys as $index => $key) {
		if (strlen($values[$index])) {
			$styles[$key] = $values[$index];
		}
	}
	return json_encode($styles);
}
