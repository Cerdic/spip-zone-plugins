<?php
/**
* Plugin SpipMotion
* par kent1 (http://kent1.sklunk.net)
* 
* Copyright (c) 2007-2008
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

$spip_spipmotion_attentes = array(
	"id_spipmotion_attente" => "BIGINT(21) NOT NULL auto_increment",
	"id_document" => "BIGINT(21) NOT NULL DEFAULT '0'",
	"id_objet" => "BIGINT(21) NOT NULL DEFAULT '0'",
	"objet" => "VARCHAR(25)",
	"id_auteur" => "BIGINT(21) NOT NULL DEFAULT '0'",
	"encode"	=> "VARCHAR(21)",
	"maj" => "TIMESTAMP"
);
$spip_spipmotion_attentes_key = array(
	"PRIMARY KEY" => "id_spipmotion_attente",
	"KEY id_document" => "id_document",
	"KEY id_objet" => "id_objet",
	"KEY encode" => "encode"
);

$tables_principales['spip_spipmotion_attentes'] = array(
	'field' => &$spip_spipmotion_attentes,
	'key' => &$spip_spipmotion_attentes_key
);

// Declarer dans la table des tables pour sauvegarde
$table_des_tables['spipmotion_attentes'] = 'spipmotion_attentes';

?>
