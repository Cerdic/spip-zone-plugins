<?php
/*
 * Spip Geomap/GoogleMap plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonz‡lez, Berio Molina
 * (c) 2007 - Distribu’do baixo licencia GNU/GPL
 *
 */

	include_spip('inc/meta');
	
	$GLOBALS['geomap_version'] = 0.1;
	
	function geomap_verifier_base(){
		$version_base = $GLOBALS['geomap_version'];
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta']['geomap_version']) )
				|| (($current_version = $GLOBALS['meta']['geomap_version'])!=$version_base)){
			effacer_meta("geomap_base_version");
			ecrire_meta('geomap_version',$current_version=$version_base,'non');
		}
		if ($current_version<0.1){
			effacer_meta("geomap_base_version");
			ecrire_meta('geomap_version',$current_version=0.1,'non');
			echo _T('geomap:miseajour') $current_version;
		}	
		ecrire_metas();
		}
	}
	
	function geomap_vider_tables() {
		effacer_meta("geomap_googlemapkey");
		effacer_meta("geomap_default_lat");
		effacer_meta("geomap_default_lonx");
		effacer_meta("geomap_default_zoom");
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

	function geomap_install($action){
	$version_base = $GLOBALS['geomap_base_version'];
		switch ($action){
			case 'test':
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
?>