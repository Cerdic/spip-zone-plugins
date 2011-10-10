<?php
/*
 * Spip Geomap/GoogleMap plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonzalez, Berio Molina
 * (c) 2007 - Distribudo baixo licencia GNU/GPL
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;
$GLOBALS['geomap_version'] = 0.2;

function geomap_verifier_base(){
	$version_base = $GLOBALS['geomap_version'];
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta']['geomap_version']) )
			|| (($current_version = $GLOBALS['meta']['geomap_version'])!=$version_base)){
		if ($current_version==0.0){
			ecrire_meta('geomap_version',$current_version=$version_base,'non');
		}
		if ($current_version<0.2){
			ecrire_meta('geomap_version',$current_version=0.2);
		}
	}
	ecrire_meta('gis_map','geomap');
}

function geomap_vider_tables(){
	ecrire_meta('gis_map','no');
	effacer_meta("geomap_googlemapkey");
	effacer_meta("geomap_version");
}

function geomap_install($action){
	$version_base = $GLOBALS['geomap_version'];
	switch ($action){
		case 'test':
			ecrire_meta('gis_map','geomap');
			return (isset($GLOBALS['meta']['geomap_version']) 
				AND ($GLOBALS['meta']['geomap_version']>=$version_base));
			break;
		case 'install':
			geomap_verifier_base();
			break;
		case 'uninstall':
			geomap_vider_tables();
			break;
	}
}
?>