<?php
//
// Les tables : 
// 1 table descriptive des noisettes et des textes
// 1 table pour les paramètres et les variables d'environnement

global $tables_principales;
global $tables_auxiliaires;

$spip_noisettes = array(
	"id_noisette" => "bigint(21) NOT NULL",
	"page" => "varchar(255) NOT NULL",
	"exclue" => "varchar(255) NOT NULL",
	"zone" => "varchar(255) NOT NULL",
	"position" => "bigint(21) NOT NULL",
	"titre" => "varchar(255) NOT NULL",
	"descriptif" => "text NOT NULL",
	"fond" => "varchar(255) NOT NULL",
	"type" => "ENUM('noisette', 'texte') DEFAULT 'noisette' NOT NULL",
	"actif" => "ENUM('non', 'oui') DEFAULT 'oui' NOT NULL",
	"maj" => "TIMESTAMP");

$spip_noisettes_key = array(
	"PRIMARY KEY" => "id_noisette");

$tables_principales['spip_noisettes'] = array(
	'field' => &$spip_noisettes,
	'key' => &$spip_noisettes_key);


$spip_params_noisettes = array(
	"id_param" => "bigint(21) NOT NULL",
	"id_noisette" => "bigint(21) NOT NULL",
	"type" => "ENUM('env', 'param', 'attribut', 'head') DEFAULT 'env' NOT NULL",
	"titre" => "varchar(255) NOT NULL",
	"descriptif" => "text NOT NULL",
	"valeur" => "varchar(255) NOT NULL",
	"id_attribut" => "bigint(21) NOT NULL",
	"maj" => "TIMESTAMP");

$spip_params_noisettes_key = array(
	"KEY id_param" => "id_param");

$tables_auxiliaires['spip_params_noisettes'] = array(
	'field' => &$spip_params_noisettes,
	'key' => &$spip_params_noisettes_key);

//-- Relations ----------------------------------------------------

global $tables_jointures;
$tables_jointures['spip_params_noisettes'][] = 'noisettes';
$tables_jointures['spip_noisettes'][] = 'params_noisettes';

//-- Table des tables ----------------------------------------------------

global $table_des_tables;
$table_des_tables['noisettes']='noisettes';
$table_des_tables['params_noisettes']='params_noisettes';

?>