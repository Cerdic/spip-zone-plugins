<?php
/**
* Plugin Notation v.0.5
* par JEM (jean-marc.viglino@ign.fr)
* 
* Copyright (c) 2007
* Logiciel libre distribue sous licence GNU/GPL.
*  
* Definition des tables
*  
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

global $tables_principales;
global $tables_auxiliaires;
global $tables_jointures;
global $table_des_tables;

$spip_notations = array(
	"id_notation" => "BIGINT(21) NOT NULL auto_increment",
	"objet"	=> "varchar(21) DEFAULT '' NOT NULL",
	"id_article" => "BIGINT(21) NOT NULL DEFAULT '0'",
	"id_forum" => "BIGINT(21) NOT NULL DEFAULT '0'",
	"id_auteur" => "BIGINT(21) NOT NULL",
	"ip"	=> "VARCHAR(255) NOT NULL",
	"note" => "TINYINT(1) NOT NULL",
	"maj" => "TIMESTAMP"
);
$spip_notations_key = array(
	"PRIMARY KEY" => "id_notation",
	"KEY objet" => "objet",
	"KEY id_article" => "id_article",
	"KEY id_forum" => "id_forum",
	"KEY id_auteur"	=> "id_auteur",
	"KEY ip" => "ip",
	"KEY note" => "note"
);
				
$spip_notations_objets = array(
	"objet"	=> "varchar(21) DEFAULT '' NOT NULL",
	"id_article" => "BIGINT(21) NOT NULL DEFAULT '0'",
	"id_forum" => "BIGINT(21) NOT NULL DEFAULT '0'",
	"note" => "DOUBLE NOT NULL",
	"note_ponderee" => "DOUBLE NOT NULL",
	"nombre_votes" => "BIGINT(21) NOT NULL"
);
$spip_notations_objets_key = array(
	"KEY objet" => "objet",
	"KEY id_article" => "id_article",
	"KEY id_forum" => "id_forum",
	"KEY note" => "note",
	"KEY note_ponderee"	=> "note_ponderee",
	"KEY nombre_votes"	=> "nombre_votes"
);

$tables_principales['spip_notations'] = array(
	'field' => &$spip_notations,
	'key' => &$spip_notations_key
);
$tables_principales['spip_notations_objets'] = array(
	'field' => &$spip_notations_objets,
	'key' => &$spip_notations_objets_key
);

// Declarer dans la table des tables pour sauvegarde
global $table_des_tables;
$table_des_tables['notations'] = 'notations';
$table_des_tables['notations_objets']  = 'notations_objets';

// Relations entre les articles et les notations
/*
global $tables_jointures;
$tables_jointures['spip_articles'][] = 'notations_articles';
$tables_jointures['spip_notations_articles'][] = 'articles';
*/

?>