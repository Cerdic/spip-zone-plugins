<?php
/**
* Plugin SPIP Geoportail
*
* @author:
* Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2010
* Logiciel distribue sous licence GNU/GPL.
*
* Definition de la structure de la base
*
**/
include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global $tables_principales, $tables_auxiliaires, $table_des_tables;

/** Table du Referentiel Geographique des Communes **/
$spip_georgc = array(
					"id_dep"		=> "char(3)", 
					"id_com"		=> "char(3)", 
					"feature_class"	=> "char(1)" , 
					"population"	=> "int(11) default '0'", 
					"surface"		=> "int(11) default '0'", 
					"zone"			=> "varchar(3) NOT NULL default 'FXX'", 
					"asciiname"		=> "varchar(200) NOT NULL default ''", 
					"name"			=> "varchar(200) NOT NULL default ''", 
					"cpostal"		=> "varchar(5) NOT NULL default ''", 
					"lon"			=> "double NOT NULL default '0'", 
					"lat"			=> "double NOT NULL default '0'", 
					"map"			=> "varchar(6) NOT NULL default ''"
				);

$spip_georgc_key = array(
					"KEY feature_class" => "feature_class",
					"KEY zone" => "zone"
				);
// C'est une table spip a ne pas sauvegarder (on peut la recreer)
$tables_auxiliaires['spip_georgc'] = array('field' => &$spip_georgc, 'key' => &$spip_georgc_key);
global $EXPORT_tables_noexport;
$EXPORT_tables_noexport[]='spip_georgc';
				
/** Table de position **/
$spip_geopositions = array(
					"id_geoposition"=> "BIGINT(21) NOT NULL auto_increment",
					"id_objet"		=> "BIGINT(21) NOT NULL default 0",
					"objet"			=> "VARCHAR(21) NOT NULL default ''",
					"lon"			=> "double NOT NULL default '0'", 
					"lat"			=> "double NOT NULL default '0'", 
					"zoom"			=> "tinyint(4)", 
					"zone"			=> "varchar(3) NOT NULL default 'FXX'",
					"id_dep"		=> "char(3)", 
					"id_com"		=> "char(3)"
				);
$spip_geopositions_key = array(
					"PRIMARY KEY"		=> "id_geoposition",
					"KEY id_rubrique"	=> "id_objet",
					"KEY id_forum"		=> "objet"
				);
$tables_principales['spip_geopositions'] = array('field' => &$spip_geopositions, 'key' => &$spip_geopositions_key); //, 'joint' => &$spip_geopositions_join);

/** Table des services **/
$spip_geoservices = array(
					"id_geoservice"	=> "BIGINT(21) NOT NULL auto_increment",
					
					"id_rubrique"	=> "BIGINT(21) NOT NULL default 0",
					"id_secteur"	=> "BIGINT(21) NOT NULL default 0",
					"statut"		=> "ENUM ('prop','publie','refuse') default 'prop'", 
					"niveau"		=> "INTEGER DEFAULT '0' NOT NULL", 
					
					"type"			=> "ENUM ('WMS', 'WMS-C', 'WFS', 'KML', 'GeoPortail') default 'WMS'", 
					"titre"			=> "TEXT NOT NULL default ''",
					"descriptif"	=> "TEXT NOT NULL default ''",
					"url_geoservice"=> "TEXT NOT NULL default ''",
					"map"			=> "TEXT NOT NULL default ''",
					"layers"		=> "TEXT NOT NULL default ''",
					"format"		=> "ENUM ('image/png', 'image/gif', 'image/jpeg', 'image/wbmp') default 'image/png'", 
					"maxextent"		=> "TEXT NOT NULL default ''",
					"minzoom"		=> "int(11) default '5'", 
					"maxzoom"		=> "int(11) default '15'", 
					"opacity"		=> "double NOT NULL default '1'", 
					"visibility"	=> "TINYINT(1) default '0'",
					"selection"		=> "TINYINT(1) default '0'",
					"zone"			=> "varchar(3) NOT NULL default 'WLD'", 
					"logo"			=> "VARCHAR(20) NOT NULL default ''",
					"link"			=> "TEXT NOT NULL default ''",
					
					"maj"			=> "TIMESTAMP"
				);

$spip_geoservices_key = array(
					"PRIMARY KEY"		=> "id_geoservice",
					"KEY id_rubrique"	=> "id_rubrique"
				);
				
$tables_principales['spip_geoservices'] = array('field' => &$spip_geoservices, 'key' => &$spip_geoservices_key);

// Declarer dans la table des tables 
global $table_des_tables;
$table_des_tables['georgc']			= 'georgc';
$table_des_tables['geopositions']	= 'geopositions';
$table_des_tables['geoservices']	= 'geoservices';

// Relations entre les tables
global $tables_jointures;
/* utiliser le critere geoposition * /
$tables_jointures['spip_articles'][] = 'geopositions';
$tables_jointures['spip_auteurs'][] = 'geopositions';
$tables_jointures['spip_rubriques'][] = 'geopositions';
$tables_jointures['spip_forums'][] = 'geopositions';
*/

?>