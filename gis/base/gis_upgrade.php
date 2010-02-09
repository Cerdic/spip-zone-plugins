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
				$res = spip_query("SELECT name, value FROM spip_gis_config WHERE name='googlemapkey'");
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
				$res = spip_query("SELECT extension FROM spip_types_documents WHERE extension='kml'");
				if (!$row = spip_fetch_array($res))
					spip_query("INSERT INTO `spip_types_documents` ( `id_type` , `titre` , `descriptif` , `extension` , `mime_type` , `inclus` , `upload` , `maj` )    VALUES ('', 'Google Earth Placemark', '', 'kml', 'application/vnd.google-earth.kml+xml', 'non', 'oui', NOW( ));");
				ecrire_meta($nom_meta_base_version,$current_version="0.1.4");
			}
			if (version_compare($current_version,"0.1.5","<")){
				spip_query("ALTER TABLE spip_gis ADD id_rubrique int(11) NULL NULL AFTER id_article");
				spip_query("ALTER TABLE spip_gis ADD INDEX ( id_rubrique )");
				ecrire_meta($nom_meta_base_version,$current_version="0.1.5");
			}
			/*esto es para realizar los cambios de nombre de variables*/
			if (version_compare($current_version,"0.1.6","<")){
				$value = "";
				$res = spip_query("SELECT name, value FROM spip_meta WHERE name='geomap_default_lat'");
				if ($row = spip_fetch_array($res)) {
					$value = $row['value'];
					ecrire_meta('gis_default_lat',$value);
				} else {
					effacer_meta('gis_default_lat');
				}
				$res = spip_query("SELECT name, value FROM spip_meta WHERE name='geomap_default_lonx'");
				if ($row = spip_fetch_array($res)) {
					$value = $row['value'];
					ecrire_meta('gis_default_lonx',$value);
				} else {
					effacer_meta('gis_default_lonx');
				}
				$res = spip_query("SELECT name, value FROM spip_meta WHERE name='geomap_default_zoom'");
				if ($row = spip_fetch_array($res)) {
					$value = $row['value'];
					ecrire_meta('gis_default_zoom',$value);
				} else {
					effacer_meta('gis_default_zoom');
				}
				effacer_meta("geomap_default_lat");
				effacer_meta("geomap_default_lonx");
				effacer_meta("geomap_default_zoom");
				ecrire_meta('gis_map', 'no');
				spip_query("ALTER TABLE spip_gis ADD zoom int(4) NULL NULL AFTER lonx");
				ecrire_meta($nom_meta_base_version,$current_version="0.1.6");
			}
			if (version_compare($current_version,"0.1.7","<")){
				spip_query("ALTER TABLE spip_gis ADD pays text NOT NULL DEFAULT '' AFTER zoom");
				spip_query("ALTER TABLE spip_gis ADD code_pays varchar(255) NOT NULL DEFAULT '' AFTER pays");
				spip_query("ALTER TABLE spip_gis ADD region text NOT NULL DEFAULT '' AFTER code_pays");
				spip_query("ALTER TABLE spip_gis ADD ville text NOT NULL DEFAULT '' AFTER region");
				spip_query("ALTER TABLE spip_gis ADD code_postal varchar(255) NOT NULL DEFAULT '' AFTER ville");
				ecrire_meta($nom_meta_base_version,$current_version="0.1.7");
			}
			/*se encaga de trasladar las variables generales de geomap a gis*/
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
