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
	
	function geomap_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| ((!version_compare($current_version = $GLOBALS['meta'][$nom_meta_base_version],$version_cible,'=')))) {
			
			if ($current_version==0.0){
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
			}
			ecrire_metas();
		}
	}
	
	function geomap_vider_tables($nom_meta_base_version) {
		effacer_meta("geomap_googlemapkey");
		effacer_meta("geomap_default_lat");
		effacer_meta("geomap_default_lonx");
		effacer_meta("geomap_default_zoom");
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>