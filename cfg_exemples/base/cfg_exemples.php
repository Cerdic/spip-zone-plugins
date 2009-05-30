<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


// faire tourner la boucle(SHOUTBOX)
$GLOBALS['table_des_tables']['cfg_exemples']='cfg_exemples';

// definir la table pour l'installation et le compilo
global $tables_principales;

$spip_cfg_exemples = array(
	"id_cfg_exemple" => "bigint(21) NOT NULL",
	"texte"	=> "longtext NOT NULL DEFAULT ''",
	"description"	=> "longtext NOT NULL DEFAULT ''",
	"date"	=> "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
	"maj" => "TIMESTAMP");
$spip_cfg_exemples_key = array(
	"PRIMARY KEY" => "id_cfg_exemple"
	);

$tables_principales['spip_cfg_exemples'] = array(
	'field' => &$spip_cfg_exemples,
	'key' => &$spip_cfg_exemples_key);

?>
