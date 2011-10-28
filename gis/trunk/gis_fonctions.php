<?php

include_spip('inc/config');

/** 
 * Filtre dec_to_dms, http://www.statemaster.com/encyclopedia/Geographic-coordinate-conversion
 * 
 * @param decimal $coord
 * @return string
 */
function dec_to_dms($coord) {
	return sprintf(
		"%0.0f° %2.3f",
		floor(abs($coord)),
		60*(abs($coord)-floor(abs($coord)))
	);
}

/** 
 * Filtre dms_to_dec, http://www.statemaster.com/encyclopedia/Geographic-coordinate-conversion
 * 
 * @param string $ref N, E, S, W
 * @param int $deg
 * @param int $min
 * @param int $sec
 * @return decimal
 */
function dms_to_dec($ref,$deg,$min,$sec) {

	$arrLatLong = array();
	$arrLatLong["N"] = 1;
	$arrLatLong["E"] = 1;
	$arrLatLong["S"] = -1;
	$arrLatLong["W"] = -1;

	return ($deg+((($min*60)+($sec))/3600)) * $arrLatLong[$ref];
}

/** 
 * Filtre distance pour renvoyer la distance entre deux points
 * http://snipplr.com/view/2531/calculate-the-distance-between-two-coordinates-latitude-longitude/
 * sinon voir ici : http://zone.spip.org/trac/spip-zone/browser/_plugins_/forms/geoforms/inc/gPoint.php
 * 
 * @param int $from id_gis du point de référence
 * @param int $ti id_gis du point distant
 * @param bool $miles renvoyer le résultat en miles (kilomètres par défaut)
 * @return
 */
function distance($from,$to,$miles=false) {
	$from = sql_fetsel('lat,lon','spip_gis',"id_gis=$from");
	$to = sql_fetsel('lat,lon','spip_gis',"id_gis=$to");
	
	$pi80 = M_PI / 180;
	$from['lat'] *= $pi80;
	$from['lon'] *= $pi80;
	$to['lat'] *= $pi80;
	$to['lon'] *= $pi80;

	$r = 6372.797; // mean radius of Earth in km
	$dlat = $to['lat'] - $from['lat'];
	$dlng = $to['lon'] - $from['lon'];
	$a = sin($dlat / 2) * sin($dlat / 2) + cos($from['lat']) * cos($to['lat']) * sin($dlng / 2) * sin($dlng / 2);
	$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
	$km = $r * $c;

	return ($miles ? ($km * 0.621371192) : $km);
}

/**
 * Critere {gis distance<XX} pour filtrer une liste de points par rapport à la distance du point de l'env
 *
 * @param unknown_type $idb
 * @param unknown_type $boucles
 * @param unknown_type $crit
 */
function critere_gis_dist($idb, &$boucles, $crit) {
	
	$boucle = &$boucles[$idb];
	$id = $boucle->primary;
	
	// exclure l'élément en cours des résultats
	$id_gis = calculer_argument_precedent($idb,$id, $boucles);
	$boucle->where[]= array("'!='", "'$boucle->id_table." . "$id'", $id_gis);
	
	// récupérer les paramètres du critère
	$op='';
	$params = $crit->param;
	$type = array_shift($params);
	$type = $type[0]->texte;
	if(preg_match(',^(\w+)([<>=]+)([0-9]+)$,',$type,$r)){
		$type=$r[1];
		$op=$r[2];
		$op_val=$r[3];
	}
	if ($op)
		$boucle->having[]= array("'".$op."'", "'".$type."'",$op_val);
	
	// récupérer lat/lon du point de la boucle englobante
	$lat = calculer_argument_precedent($idb,'lat', $boucles);
	$lon = calculer_argument_precedent($idb,'lon', $boucles);
	
	// http://www.awelty.fr/developpement-web/php/
	// http://www.movable-type.co.uk/scripts/latlong-db.html
	// http://code.google.com/intl/fr/apis/maps/articles/geospatial.html#geospatial
	$select = "(6371 * acos( cos( radians(\".$lat.\") ) * cos( radians( gis.lat ) ) * cos( radians( gis.lon ) - radians(\".$lon.\") ) + sin( radians(\".$lat.\") ) * sin( radians( gis.lat ) ) ) ) AS distance";
	$order = "'distance'";
	
	$boucle->select[]= $select;
	$boucle->order[]= $order;
	
}

/**
 * Balise #DISTANCE issue du critère {gis distance<XX}
 * merci marcimant : http://formation.magraine.net/spip.php?article61
 *
 * @param unknown_type $p
 */
function balise_distance_dist($p) {
	return rindex_pile($p, 'distance', 'gis');
}

function generer_url_ecrire_gis($id, $args='', $ancre='', $statut='', $connect='') {
	$a = "id_gis=" . intval($id);
	if (!$statut) {
		$statut = sql_getfetsel('statut', 'spip_articles', $a,'','','','',$connect);
	}
	$h = generer_url_ecrire('gis', $a . ($args ? "&$args" : ''))
	. ($ancre ? "#$ancre" : '');
	return $h;
}

/**
 * Définition de l'API à utiliser en prenant compte les defines
 */
function gis_api_utilisee(){
	$defaut = 'openlayers';
	if(defined('_GIS_APIS_FORCEE')){
		return _GIS_APIS_FORCEE;
	}else{
		if(defined('_GIS_APIS_DEFAUT')){
			$defaut = _GIS_APIS_DEFAUT;
		}
		$config = lire_config('gis/api');
		return $config ? $config : $defaut;
	}
}

/**
 * Définition du fond cartographique à utiliser en prenant compte les defines
 */
function gis_maptype_utilise(){
	$defaut = 'ROAD';
	if(defined('_GIS_MAPTYPES_FORCE')){
		return _GIS_MAPTYPES_FORCE;
	}else{
		if(defined('_GIS_MAPTYPES_DEFAUT')){
			$defaut = _GIS_MAPTYPES_DEFAUT;
		}
		$config = lire_config('gis/maptype');
		return $config ? $config : $defaut;
	}
}
?>