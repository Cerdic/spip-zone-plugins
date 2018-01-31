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



/**
 * Renvoie une géométrie couvrant tous les points à une distance donnée de la géométrie d'entrée.
 *
 * Cela permet de calculer une zone autour d’un point, d’une ligne ou d’un polygone, à une certaine distance.
 * Retourne une géométrie (polygone) simplifiée.
 *
 * @filtre
 * @example
 *     ```
 *     Dans une boucle GIS :
 *     #SET{poly,#GEOMETRY|zoner_geometrie{wkt,0.05}}
 *     #SET{poly,#GEOMETRY|zoner_geometrie{wkt,0.05,0.005}}
 *     ```
 *
 * @note
 *     Nécessite Mysql 5.6+ ou Mariadb.
 *
 * @param string $geometrie
 *     Géométrie
 * @param float $distance
 *     En degrés décimal (si l’unité de géométrie est latitude / longitude…)
 * @param float|null $tolerance
 *     Tolérance de simplification des tracés (null, appliquera la valeur par défaut ($distance/20)),
 *     en degré décimal (si l’unité de géométrie est latitude / longitude…) ;
 * @return mixed
 */
function zoner_geometrie($geometrie, $type = 'wkt', $distance = 0.05, $tolerance = null) {
	// tolérance… à peu près 1/20 de l’unité de distance (pifomètre)
	if (is_null($tolerance)) {
		$tolerance = $distance / 20;
	}

	// Simplifier le tracé d’origine… le récupérer en wkt
	$geometrie = simplifier_geometrie($geometrie, $type, $tolerance, 'wkt');

	// Obtenir le zonage demandé
	$select = 'GeomFromText(' . sql_quote($geometrie) . ')';
	$select = 'ST_Buffer(' . $select . ', ' . floatval($distance) . ')';
	#$select = 'ST_Simplify(' . $select . ', ' . floatval($tolerance) . ')'; // mysql 5.7+ (non testé)
	$select = 'ST_AsText(' . $select . ')';
	$zone = sql_getfetsel($select);

	// Simplifier le tracé de la zone, le récupérer au format d’entrée
	$zone = simplifier_geometrie($zone, 'wkt', $tolerance, $type);
	return $zone;
}

/**
 * Simplifier une forme géométrique
 *
 * Diminue le nombre de points d’une forme géométrique (lignes, polygones, ...)
 *
 * @param mixed $data
 *     Forme à simplifer, pouvant être lue par geoPHP (json, wkt, ...)
 * @param string $type
 *     Type d’entrée de `$data` (json, wkt, ...)
 * @param float|null $tolerance
 *     Tolérance de la réduction (en degré décimal si les unités sont latitude/longitude)
 * @param string $type_sortie
 *     Type de sortie de la simplification de `$data` (json, wkt, ...)
 *     Par défaut, le type indiqué en entrée.
 * @retrun mixed
 *     Forme simplifiée, dans le même format qu’en entrée.
 */
function simplifier_geometrie($data, $type = 'wkt', $tolerance = null, $type_sortie = null) {
	if (!class_exists('geoPHP_Simplify')) {
		include_spip('inc/geoPHP_Simplify');
	}
	$geometry = geoPHP::load($data, $type);
	$geometry = geoPHP::geometryReduce($geometry);
	$geometry = geoPHP_Simplify::geometrySimplify($geometry, $tolerance);
	$data = $geometry->out($type_sortie ? $type_sortie : $type);
	return $data;
}


/**
 * Compile le critère `geometrie_dans_polygone` qui sélectionne des points contenus dans un polygone donné.
 *
 * @critere
 * @see zoner_geometrie() pour créer une zone (polygone) à partir d’un tracé.
 * @example
 *     ```
 *     Dans une boucle GIS : #SET{poly,#GEOMETRY|zoner_geometrie{wkt,0.05}}
 *     Puis :
 *     <BOUCLE_points_proches(GIS){type=Point}{geometrie_dans_polygone #GET{poly}}> ...
 *     ```
 * @note
 *     - Nécessite Mysql 5.6 ou Mariadb
 *     - Si le polygone est trop complexe (nombreux points, le calcul peut être trop long),
 *       c'est pour cela que zoner_geometrie() simplifie le tracé automatiquement.
 *
 * @param string $idb Identifiant de la boucle
 * @param array $boucles AST du squelette
 * @param Critere $crit Paramètres du critère dans cette boucle
 * @return void
 **/
function critere_geometrie_dans_polygone_dist($idb, &$boucles, $crit) {
	$not = $crit->not;
	$boucle = &$boucles[$idb];

	// prendre en priorite un identifiant en parametre {branche XX}
	if (isset($crit->param[0])) {
		$polygon = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
		// sinon on le prend chez une boucle parente
	} else {
		return array('zbug_critere_inconnu', array('critere' => $crit->op));
	}

	$polygon = kwote($polygon, $boucle->sql_serveur, 'TEXT');
	if ($not) {
		$boucle->where[] = array("'='", "'ST_WITHIN(gis.geo, GeomFromText(' . $polygon . '))'", "'0'");
	} else {
		$boucle->where[] = array("'='", "'ST_WITHIN(gis.geo, GeomFromText(' . $polygon . '))'", "'1'");
	}

}