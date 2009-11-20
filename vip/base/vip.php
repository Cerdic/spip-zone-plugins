<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


$GLOBALS['table_des_tables']['vips']='vips';

$spip_vip = array(
	"id_vip" => "bigint(11) NOT NULL",
	"titre"	=> "varchar(255) NOT NULL DEFAULT ''",
	"qui"	=> "varchar(255) NOT NULL DEFAULT ''",
	"faire"	=> "varchar(255) NOT NULL DEFAULT ''",
	"sur"	=> "varchar(255) NOT NULL DEFAULT ''",
	"quoi"	=> "varchar(255) NOT NULL DEFAULT ''",
	"options" => "longtext NOT NULL DEFAULT ''");
$spip_vip_key = array(
	"PRIMARY KEY" => "id_vip",
	"KEY qui" => "qui",
	"KEY faire" => "faire",
	"KEY sur" => "sur",
	"KEY quoi" => "quoi"
	);

$GLOBALS['tables_principales']['spip_vips'] = array(
	'field' => &$spip_vip,
	'key' => &$spip_vip_key);

$GLOBALS['table_primary']['spip_vips']="id_vip";
?>
