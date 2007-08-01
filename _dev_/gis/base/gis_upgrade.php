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
			if (version_compare($current_version,"0.1.3","<")){
				spip_query("CREATE TABLE `spip_gis_mots` (
					`id_gis` bigint(21) NOT NULL auto_increment,
					`id_mot` int(11) default NULL,
					`lat` float default NULL,
					`lonx` float default NULL,
					`zoom` tinyint(4) default NULL,
					PRIMARY KEY  (`id_gis`)
				)");
				ecrire_meta($nom_meta_base_version,$current_version="0.1.3");
			}
			if (version_compare($current_version,"0.1.4","<")){
				$res = spip_query("SELECT * FROM spip_types_documents WHERE extension='kml'");
				if (!$row = spip_fetch_array($res))
					spip_query("INSERT INTO `spip_types_documents` ( `id_type` , `titre` , `descriptif` , `extension` , `mime_type` , `inclus` , `upload` , `maj` )    VALUES ('', 'Google Earth Placemark', '', 'kml', 'application/vnd.google-earth.kml+xml kml', 'non', 'oui', NOW( ));");
				ecrire_meta($nom_meta_base_version,$current_version="0.1.4");
			}
			ecrire_metas();
		}
	}
	
	function gis_vider_tables($nom_meta_base_version) {
		spip_query("DROP TABLE spip_gis");
		spip_query("DROP TABLE spip_gis_mots");
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>