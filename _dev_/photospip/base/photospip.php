<?php

/*
 * Photospip
 * Un Photoshop-lite dans spip?
 *
 * Auteurs :
 * Quentin Drouet
 *
 * © 2008 - Distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

$documents_inters = array(
	"id_documents_inter"	=> "bigint(21) NOT NULL AUTO_INCREMENT",
	"id_document"	=> "bigint(21) NOT NULL", //document original
	"id_auteur"	=> "bigint(21) NOT NULL", //qui a modifié
	"extension"	=> "VARCHAR(10) DEFAULT '' NOT NULL",
	"fichier"	=> "varchar(255) DEFAULT '' NOT NULL",
	"taille"	=> "integer",
	"largeur"	=> "integer",
	"hauteur"	=> "integer",
	"mode"	=> "ENUM('vignette', 'image', 'document') DEFAULT 'document' NOT NULL",
	"version" => "bigint(21) NOT NULL",
	"filtre" => "text",
	"param" => "text",
	"maj" 	=> "TIMESTAMP", //quand ca a eut lieu
);
  	
$documents_inters_key = array(
	"PRIMARY KEY" => "id_documents_inter, id_document",
	"KEY id_document"	=> "id_document",
	"KEY id_auteur"	=> "id_auteur");

global $tables_principales;
$tables_principales['spip_documents_inters'] = array(
	'field' => &$documents_inters,
	'key' => &$documents_inters_key);

global $tables_jointures;
$tables_jointures['spip_documents_inters'][] = 'documents';
$tables_jointures['spip_documents_inters'][] = 'documents_articles';
$tables_jointures['spip_documents_inters'][] = 'auteurs';

global $table_des_tables;
$table_des_tables['documents_inters']='documents_inters';
?>
