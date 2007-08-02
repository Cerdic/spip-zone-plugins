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

global $tables_principales;
global $tables_auxiliaires;

//taboa gis  ------------------------------------------
$spip_gis = array(
	"id_gis" 	=> "bigint(21) NOT NULL",
	"id_article" => "int(11) NULL NULL",
	"lat" => "float(21)  NULL NULL",
	"lonx" => "float(21)  NULL NULL"
	);
	
$spip_gis_key = array(
	"PRIMARY KEY" => "id_gis",
	"KEY id_article" => "id_article"
	);
$spip_gis_join = array(
	"id_article"=>"id_article"
	);

$tables_principales['spip_gis'] = array(
	'field' => &$spip_gis,
	'key' => &$spip_gis_key,
	'joint' => &$spip_gis_join
	);
//------------------------------------------------------	
//taboa gis_mots  --------------------------------------
$spip_gis_mots = array(
	"id_gis" 	=> "bigint(21) NOT NULL",
	"id_mot" => "int(11) NULL NULL",
	"lat" => "float(21)  NULL NULL",
	"lonx" => "float(21)  NULL NULL",
	"zoom" => "tinyint(4)  NULL NULL"
	);
	
$spip_gis_mots_key = array(
	"PRIMARY KEY" => "id_gis",
	"KEY id_mot" => "id_mot"
	);
$spip_gis_mots_join = array(
	"id_mot"=>"id_mot"
	);

$tables_principales['spip_gis_mots'] = array(
	'field' => &$spip_gis_mots,
	'key' => &$spip_gis_mots_key,
	'joint' => &$spip_gis_most_join
	);
//------------------------------------------------------	
//on ajoute les kml à la table spip_types_documents  --------------------------------------
$res = spip_query("SELECT extension FROM spip_types_documents WHERE extension='kml'");
if (!$row = spip_fetch_array($res))
	spip_query("INSERT INTO `spip_types_documents` ( `id_type` , `titre` , `descriptif` , `extension` , `mime_type` , `inclus` , `upload` , `maj` )    VALUES ('', 'Google Earth Placemark', '', 'kml', 'application/vnd.google-earth.kml+xml', 'non', 'oui', NOW( ));");

//-- Relaci—ns ----------------------------------------------------
global $table_des_tables;
$table_des_tables['gis']='gis';
$table_des_tables['gis_config']='gis_config';
$table_des_tables['gis_mots']='gis_mots';
//-- Jointures ----------------------------------------------------
global $tables_jointures;
$tables_jointures['spip_articles'][]= 'gis';
$tables_jointures['spip_gis'][] = 'articles';
$tables_jointures['spip_mots'][]= 'gis_mots';
$tables_jointures['spip_gis_mots'][] = 'mots';
?>