<?php
/*
 * GeoForms
 * Geolocalistion dans les tables et les formulaires
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 * Systemes de Coordonnees Lambert et Lattitude/Longitude
 * source : http://fr.wikipedia.org/wiki/Projection_de_Lambert
 */

function geoforms_liste_projections(){
	return array_keys($GLOBALS['projections_lambert']);
}
// pouvoir traiter des systemes de differentes natures
function geoforms_systeme_vers_lat_lont($lat,$long,$systeme){
	if (isset($GLOBALS['projections_lambert'][$systeme]))
		return geoforms_lambert_vers_lat_long($lat,$long,$systeme);
	return array($lat,$long);
}

$GLOBALS['projections_lambert']=array(
'lambert1'=>array('fi0'=>55*0.9,'fi1'=>54*0.9,'fi2'=>56*0.9,'l0'=>2+20/60+14.025/3600,'X'=>600000,'Y'=>1200000),
'lambert2'=>array('fi0'=>52*0.9,'fi1'=>51*0.9,'fi2'=>53*0.9,'l0'=>2+20/60+14.025/3600,'X'=>600000,'Y'=>2200000),
'lambert3'=>array('fi0'=>49*0.9,'fi1'=>48*0.9,'fi2'=>50*0.9,'l0'=>2+20/60+14.025/3600,'X'=>600000,'Y'=>3200000),
'lambert4'=>array('fi0'=>46.85*0.9,'fi1'=>46.17821*0.9,'fi2'=>47.51963*0.9,'l0'=>2+20/60+14.025/3600,'X'=>234,'Y'=>4185861),
'lambertgc'=>array('fi0'=>47,'fi1'=>45,'fi2'=>49,'l0'=>2+20/60+14.025/3600,'X'=>600000,'Y'=>600000),
'lambert93'=>array('fi0'=>46.5,'fi1'=>44,'fi2'=>49,'l0'=>3,'X'=>700000,'Y'=>6600000),
);
$terre_a=6378137;
$terre_f=1/298.257222101;
$terre_b=$terre_a*(1-$terre_f);

// conversion lattitude longitude (en degres) vers lambert
function geoforms_lat_long_vers_lambert($lat,$long,$lambert){
	static $converter=array();
	if (!isset($converter[$lambert])){
		include_spip('inc/gPoint');
		$converter[$lambert] = new gPoint();
		$converter[$lambert]->configLambertProjection(
			$GLOBALS['projections_lambert'][$lambert]['X'],
			$GLOBALS['projections_lambert'][$lambert]['Y'],
			$GLOBALS['projections_lambert'][$lambert]['l0'],
			$GLOBALS['projections_lambert'][$lambert]['fi0'],
			$GLOBALS['projections_lambert'][$lambert]['fi1'],
			$GLOBALS['projections_lambert'][$lambert]['fi2']);
	}
	$converter[$lambert]->setLongLat($long,$lat);
	$converter[$lambert]->convertLLtoLCC();
	return array($converter[$lambert]->lccE(),$converter[$lambert]->lccN());
}

function geoforms_lambert_vers_lat_long($X,$Y,$lambert,$eps=0.001){
	static $converter=array();
	if (!isset($converter[$lambert])){
		include_spip('inc/gPoint');
		$converter[$lambert] = new gPoint();
		$converter[$lambert]->configLambertProjection(
			$GLOBALS['projections_lambert'][$lambert]['X'],
			$GLOBALS['projections_lambert'][$lambert]['Y'],
			$GLOBALS['projections_lambert'][$lambert]['l0'],
			$GLOBALS['projections_lambert'][$lambert]['fi0'],
			$GLOBALS['projections_lambert'][$lambert]['fi1'],
			$GLOBALS['projections_lambert'][$lambert]['fi2']);
	}
	// attention, prendre en compte l'omission eventuelle des millions dans le Y du lambert
	if ($Y<1000000 AND in_array($lambert,array('lambert1','lambert2','lambert3','lambert4'))){
		$million = intval(substr($lambert,-1))*1000000;
		$Y = $Y+$million;
	}
	$converter[$lambert]->setLambert($X,$Y);
	$converter[$lambert]->convertLCCtoLL();
	return array($converter[$lambert]->Lat(),$converter[$lambert]->Long());
}
?>