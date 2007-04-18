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

// pouvoir traiter des systemes de differentes natures
function geoforms_systeme_vers_lat_lont($lat,$lont,$systeme){
	return geoforms_lat_long_vers_lambert($lat,$lont,$systeme);
}

$GLOBALS['projections_lambert']=array(
'lambert1'=>array('fi0'=>55*0.9,'fi1'=>54*0.9,'fi2'=>56*0.9,'l0'=>3,'X'=>600000,'Y'=>1200000),
'lambert2'=>array('fi0'=>52*0.9,'fi1'=>51*0.9,'fi2'=>53*0.9,'l0'=>3,'X'=>600000,'Y'=>2200000),
'lambert3'=>array('fi0'=>49*0.9,'fi1'=>48*0.9,'fi2'=>50*0.9,'l0'=>3,'X'=>600000,'Y'=>3200000),
'lambert4'=>array('fi0'=>46.85*0.9,'fi1'=>46.17821*0.9,'fi2'=>47.51963*0.9,'l0'=>3,'X'=>234,'Y'=>4185861),
'lambertgc'=>array('fi0'=>47,'fi1'=>45,'fi2'=>49,'l0'=>3,'X'=>600000,'Y'=>600000),
'lambert93'=>array('fi0'=>46.5,'fi1'=>44,'fi2'=>49,'l0'=>3,'X'=>700000,'Y'=>6600000),
);
$terre_a=6378137;
$terre_f=1/298.257222101;
$terre_b=$terre_a*(1-$terre_f);

// conversion lattitude longitude (en degres) vers lambert
function geoforms_lat_long_vers_lambert($lat,$long,$lambert){
	$pi = pi();
	$e = exp(1);
	$lat = $lat*$pi/180.0;
	$long = $long*$pi/180.0;
	$fi0 = $GLOBALS['projections_lambert'][$lambert]['fi0']*$pi/180.0;
	$fi1 = $GLOBALS['projections_lambert'][$lambert]['fi1']*$pi/180.0;
	$fi2 = $GLOBALS['projections_lambert'][$lambert]['fi2']*$pi/180.0;
	$l0 = $GLOBALS['projections_lambert'][$lambert]['l0']*$pi/180.0;
	$X0 = $GLOBALS['projections_lambert'][$lambert]['X'];
	$Y0 = $GLOBALS['projections_lambert'][$lambert]['Y'];
	
	$s1=sin($fi1);
	$s2=sin($fi2);
	$c1=cos($fi1);
	$c2=cos($fi2);
	$n = log($c2/$c1) + 0.5*log( (1.0-$e*$e*$s1*$s1) / (1.0-$e*$e*$s2*$s2) );
	$n = $n/log( (tan($fi1/2.0+$pi/4)*pow(1-$e*$s1,0.5*$e)*pow(1+$e*$s2,0.5*$e)) / (tan($fi2/2+$pi/4)*pow(1+$e*$s1,0.5*$e)*pow(1-$e*$s2,0.5*$e)) );
	
	$ro0 = $terre_a*$c1/($n*sqrt(1-$e*$e*$s1*$s1))*pow(tan($f1/2+$pi/4)*pow((1-$e*$s1)/(1+$e*$s1),0.5*$e),$n);
	
	$ro = $ro0*pow(pow((1+$e*sin($lat))/(1-$e*sin($lat)),0.5*$e)/tan($lat/2+$pi/4),$n);
	$teta = $n*($long-$l0);
	$X = $X0 + $ro * sin($teta);
	$Y = $Y0 + $fi0-$ro*cos($teta);
	return array($X,$Y);
}

function geoforms_lambert_vers_lat_long($X,$Y,$lambert,$eps=0.001){
	$pi = pi();
	$e = exp(1);
	$fi0 = $GLOBALS['projections_lambert'][$lambert]['fi0']*$pi/180.0;
	$fi1 = $GLOBALS['projections_lambert'][$lambert]['fi1']*$pi/180.0;
	$fi2 = $GLOBALS['projections_lambert'][$lambert]['fi2']*$pi/180.0;
	$l0 = $GLOBALS['projections_lambert'][$lambert]['l0']*$pi/180.0;
	$X0 = $GLOBALS['projections_lambert'][$lambert]['X'];
	$Y0 = $GLOBALS['projections_lambert'][$lambert]['Y'];
	
	$eps = $eps*$pi/180.0;
	$maxiter = 100;
	
	$s1=sin($fi1);
	$s2=sin($fi2);
	$c1=cos($fi1);
	$c2=cos($fi2);
	$n = log($c2/$c1) + 0.5*log( (1.0-$e*$e*$s1*$s1) / (1.0-$e*$e*$s2*$s2) );
	$n = $n/log( (tan($fi1/2.0+$pi/4)*pow(1-$e*$s1,0.5*$e)*pow(1+$e*$s2,0.5*$e)) / (tan($fi2/2+$pi/4)*pow(1+$e*$s1,0.5*$e)*pow(1-$e*$s2,0.5*$e)) );
	
	$ro0 = $terre_a*$c1/($n*sqrt(1-$e*$e*$s1*$s1))*pow(tan($f1/2+$pi/4)*pow((1-$e*$s1)/(1+$e*$s1),0.5*$e),$n);

	$ro = sqrt(($X-$X0)*($X-$X0)+($Y0-$Y+$fi0)*($Y0-$Y+$fi0));
	$teta = 2*atan(($X-$X0)/($Y0-$Y+$fi0+$ro));
	
	$long = $teta/$n+$l0;
	$ro0surro=pow($ro0/$ro,1/$n);
	$lat = 2*atan($ro0surro)-0.5*$pi;
	do {
		$lat0 = $lat;
		$s = sin($lat0);
		$lat = 2*atan($ro0surro*pow( (1+$e*$s)/(1-$e*$s),0.5*$e))-0.5*$pi;
	} while (abs($lat-$lat0)>$eps && $maxiter--);
	return array($lat*180.0/$pi,$long*180.0/$pi);
}
?>