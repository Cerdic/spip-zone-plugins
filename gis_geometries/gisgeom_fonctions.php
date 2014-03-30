<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

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
 * Filtre wkt_to_json converti une chaine au format WKT en GeoJSON
 * 
 * @param string $wkt
 * @return string
 */
function wkt_to_json($wkt) {
	if (!$wkt) return false;
	// include_spip cherche les fichier .php, on utilise find_in_path avec l'option $include à true
	find_in_path(_DIR_LIB_GEOPHP.'geoPHP.inc', '', true);
	$geometry = geoPHP::load($wkt,'wkt');
	return $geometry->out('json');
}

/**
 * Filtre json_to_wkt converti une chaine au format GeoJSON en WKT
 * 
 * @param string $json
 * @return string
 */
function json_to_wkt($json) {
	if (!$json) return false;
	find_in_path(_DIR_LIB_GEOPHP.'geoPHP.inc', '', true);
	$geometry = geoPHP::load($json,'json');
	return $geometry->out('wkt');
}

/**
 * Filtre wkt_to_kml converti une chaine au format WKT en KML
 * 
 * @param string $wkt
 * @return string
 */
function wkt_to_kml($wkt) {
	if (!$wkt) return false;
	// include_spip cherche les fichier .php, on utilise find_in_path avec l'option $include à true
	find_in_path(_DIR_LIB_GEOPHP.'geoPHP.inc', '', true);
	$geometry = geoPHP::load($wkt,'wkt');
	return $geometry->out('kml');
}

/**
 * Filtre wkt_to_gpx converti une chaine au format WKT en GPX
 * 
 * @param string $wkt
 * @return string
 */
function wkt_to_gpx($wkt) {
	if (!$wkt) return false;
	// include_spip cherche les fichier .php, on utilise find_in_path avec l'option $include à true
	find_in_path(_DIR_LIB_GEOPHP.'geoPHP.inc', '', true);
	$geometry = geoPHP::load($wkt,'wkt');
	return $geometry->out('gpx');
}

?>