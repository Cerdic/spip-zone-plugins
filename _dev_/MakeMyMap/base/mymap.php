<?php
/*
 * Spip mymap plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonz‡lez, Berio Molina
 * (c) 2007 - Distribu’do baixo licencia GNU/GPL
 *
 */
 
/****************DECLARATION DE MES TABLES***************/
function mymap_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['mymap']='mymap';
	$interface['table_des_tables']['mymap_config']='mymap_config';
	$interface['table_des_tables']['mymap_articles']='mymap_articles';
	return $interface;
}

/****************DECLARATION LA STRUCTURE DE MES TABLES***************/
function mymap_declarer_tables_principales($tables_principales){

$spip_mymap = array(
	"id_mymap" 	=> "bigint(21) NOT NULL",
	"id_article" => "int(11) NULL NULL",
	"lat" => "float(21)  NULL NULL",
	"lonx" => "float(21)  NULL NULL",
	"marker" => "text  NULL NULL",
	"descriptif" => "text  NULL NULL"
	);	
$spip_mymap_key = array(
	"PRIMARY KEY" => "id_mymap",
	"KEY id_article" => "id_article"
	);


$tables_principales['spip_mymap'] = array(
	'field' => &$spip_mymap,
	'key' => &$spip_mymap_key,
	'joint' => &$spip_mymap_join
	);


$spip_mymap_articles = array(
	"id_article" => "int(11) NOT NULL",
	"lat" => "float(21)  default NULL",
	"lonx" => "float(21)  default NULL",
	"zoom" => "tinyint(4) default NULL",
	"descriptif" => "text  default NULL"
	);	
$spip_mymap_articles_key = array(
	"PRIMARY KEY" => "id_article"
	);


$tables_principales['spip_mymap_articles'] = array(
	'field' => &$spip_mymap_articles,
	'key' => &$spip_mymap_articles_key
	);	

//on ajoute les kml à la table spip_types_documents  --------------------------------------
$res = spip_query("SELECT extension FROM spip_types_documents WHERE extension='kml'");
if (!$row = spip_fetch_array($res)){
	spip_query("INSERT INTO `spip_types_documents` ( `id_type` , `titre` , `descriptif` , `extension` , `mime_type` , `inclus` , `upload` , `maj` )    VALUES ('', 'Google Earth Placemark', '', 'kml', 'application/vnd.google-earth.kml+xml', 'non', 'oui', NOW( ));");
}

return $tables_principales;
}
/****************************TABLES AUXILIAIRES***********************/
function mymap_declarer_tables_auxiliaires($tables_auxiliaires){
	return $tables_auxiliaires;
}


	include_spip('inc/meta');
	
	function mymap_upgrade($nom_meta_base_version,$version_cible){

		/*echo "======================$version_cible++++===========";
		echo "======================$nom_meta_base_version++++===========";
		echo "======================".$GLOBALS['meta'][$nom_meta_base_version]."+++===========";*/
		$GLOBALS['mymap_version'] = 0.2;
		$current_version = 0.0;
		
					/*********************PARTIE POUR L'INTERFACE DE L'API GMAP****************************/
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
					//die("la3");
			if ($current_version==0.0){
				//die("Version=0.0");
				include_spip('base/mymap');
				include_spip('base/create');
				creer_base();
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
			}
			if(version_compare($current_version,"0.1.1","<")){
				spip_query("ALTER TABLE spip_mymap ADD INDEX ( id_article )");
				ecrire_meta($nom_meta_base_version,$current_version="0.1.1");
			}
			if(version_compare($current_version,"0.1.2","<")){
				$key = "";
				$res = spip_query("SELECT name, value FROM spip_mymap_config WHERE name='googlemapkey'");
				if ($row = spip_fetch_array($res))
					$key = $row['value'];
				ecrire_meta('mymap_googlemapkey',$key);
				spip_query("DROP TABLE spip_mymap_config");
				ecrire_meta($nom_meta_base_version,$current_version="0.1.2");
			}
			if (version_compare($current_version,"0.1.3","<")){
				spip_query("CREATE TABLE `spip_mymap_mots` (
							`id_mymap` bigint(21) NOT NULL auto_increment,
							`id_mot` int(11) default NULL,
							`lat` float default NULL,
							`lonx` float default NULL,
							`zoom` tinyint(4) default NULL,
							PRIMARY KEY  (`id_mymap`)
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
				$res = spip_query("ALTER TABLE `spip_mymap` ADD `descriptif` TEXT NULL DEFAULT NULL ");
				ecrire_meta($nom_meta_base_version,$current_version="0.1.5");
				$res = spip_query("SELECT * FROM spip_mymap");
				while ($row = spip_fetch_array($res)){
					$res2 = spip_query("SELECT chapo FROM spip_articles WHERE id_article='".$row["id_article"]."' ");
					while ($row2 = spip_fetch_array($res2)){
						$chapo = $row2["chapo"];
						spip_query("UPDATE spip_mymap SET descriptif ='".mysql_escape_string($chapo)."' WHERE id_article='".$row["id_article"]."' ");
					}
				}
				ecrire_meta($nom_meta_base_version,$current_version="0.1.5");
				
			}		
			if (version_compare($current_version,"0.1.6","<")){			
				$res = spip_query("ALTER TABLE `spip_mymap` ADD `marker` TEXT NULL DEFAULT NULL ");
				ecrire_meta($nom_meta_base_version,$current_version="0.1.6");				
			}		
			if (version_compare($current_version,"0.1.7","<")){	
				//die();
				//TABLE INUTILE
				$res = spip_query("DROP TABLE `spip_mymap_mots`");
				//TABLE POUR CENTRER LES CARTES INDIVIDUELLEMENT
				spip_query("CREATE TABLE `spip_mymap_articles` (
					`id_article` bigint(21) NOT NULL,
					`lat` float default NULL,
					`lonx` float default NULL,
					`zoom` tinyint(4) default NULL,
					PRIMARY KEY  (`id_article`)
				)");
				//RECUPERATION DES ZOOM ET COORDONNES PAR DEFAUT
				if (!strlen($view_zoom) OR !is_numeric($view_zoom)){
				$view_zoom = isset($GLOBALS['meta']['mymap_default_zoom'])?$GLOBALS['meta']['mymap_default_zoom']:'8'; 
				if (!strlen($view_zoom) OR !is_numeric($view_zoom)) $view_zoom='8';
				}
				if(sizeof($view_lat)==0 OR (sizeof($view_lat)> 0  AND !is_numeric($view_lat))){
				$view_lat = isset($GLOBALS['meta']['mymap_default_lat'])?$GLOBALS['meta']['mymap_default_lat']:'47.15984'; 
				if (!strlen($view_lat) OR !is_numeric($view_lat)) $view_lat='47.15984';
				}	
				if(sizeof($view_long)==0 OR (sizeof($view_long)> 0  AND !is_numeric($view_long))){
				$view_long = isset($GLOBALS['meta']['mymap_default_lonx'])?$GLOBALS['meta']['mymap_default_lonx']:'2.988281'; 
				if (!strlen($view_long) OR !is_numeric($view_long)) $view_long='2.988281';
				}
				//REMPLISSAGE DES ZOOM ET COORDONNES PAR DEFAUT
				$res2 = spip_query("SELECT distinct(id_article) FROM spip_mymap");
				while ($row = spip_fetch_array($res2)){
					spip_query("INSERT INTO spip_mymap_articles ( `id_article` , `lat` , `lonx` , `zoom` ) VALUES ('".$row["id_article"]."', '".$view_lat."', '".$view_long."', '".$view_zoom."')");
				}
				
				ecrire_meta($nom_meta_base_version,$current_version="0.1.7");				
			}
			
			
			ecrire_metas();
			
			/********************ICI L'API GMAP***************/
			mymap_verifier_base();	
			
		}
				
	}
	
	function mymap_vider_tables($nom_meta_base_version) {
		spip_query("DROP TABLE spip_mymap");
		spip_query("DROP TABLE spip_mymap_mots");
		spip_query("DROP TABLE spip_mymap_articles");
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
		
		/********************ICI L'API GMAP***************/
		mymap_vider_tables();
	}
	
	
	/***********************************************************************
						PARTIE POUR L'API GMAP
	***********************************************************************/
	$GLOBALS['mymap_version'] = 0.2;
	
	function mymap_verifier_base(){
		$version_base = $GLOBALS['mymap_version'];
		$current_version = 0.0;
		if ((!isset($GLOBALS['meta']['mymap_version']) )
				|| (($current_version = $GLOBALS['meta']['mymap_version'])!=$version_base)){
			if ($current_version==0.0){
				ecrire_meta('mymap_version',$current_version=$version_base,'non');
			}
			if ($current_version<0.2){
				ecrire_meta('mymap_version',$current_version=0.2);
			}
		}
		effacer_meta("mymap_googlemapkey");
		effacer_meta("mymap_default_lat");
		effacer_meta("mymap_default_lonx");
		effacer_meta("mymap_default_zoom");
		effacer_meta("mymap_version");
		effacer_meta("mymap_chemin");
		ecrire_metas();
	}
	


?>