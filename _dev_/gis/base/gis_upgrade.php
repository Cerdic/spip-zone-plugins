<?php
/*
 * Spip Gis plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonz‡lez, Berio Molina
 * (c) 2007 - Distribu’do baixo licencia GNU/GPL
 *
 */

	include_spip('inc/meta');
	
	function gis_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			
			if ($current_version==0.0){
				include_spip('base/gis');
				include_spip('base/create');
				creer_base();
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
			}

			ecrire_metas();
		}
	}
	
	function gis_vider_tables($nom_meta_base_version) {
		spip_query("DROP TABLE spip_gis");
		spip_query("DROP TABLE spip_gis_config");
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>