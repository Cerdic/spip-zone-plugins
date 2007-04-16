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
			if (version_compare($current_version,"0.1.1","<")){
				spip_query("ALTER TABLE spip_gis ADD INDEX ( id_article )");
				ecrire_meta($nom_meta_base_version,$current_version="0.1.1");
			}
			if (version_compare($current_version,"0.1.2","<")){
				$key = "";
				$res = spip_query("SELECT * FROM spip_gis_config WHERE name='googlemapkey'");
				if ($row = spip_fetch_array($res))
					$key = $row['value'];
				ecrire_meta('geomap_googlemapkey',$key);
				spip_query("DROP TABLE spip_gis_config");
				ecrire_meta($nom_meta_base_version,$current_version="0.1.2");
			}
			ecrire_metas();
		}
	}
	
	function gis_vider_tables($nom_meta_base_version) {
		spip_query("DROP TABLE spip_gis");
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>