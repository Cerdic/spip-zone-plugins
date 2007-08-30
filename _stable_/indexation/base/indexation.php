<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

global $tables_auxiliaires;

$spip_indexation = array(
	"id" => "bigint(21) NOT NULL DEFAULT 0",
	"type" => "int NOT NULL DEFAULT 0",
	"titre"	=> "text NOT NULL DEFAULT ''",
	"meta"	=> "text NOT NULL DEFAULT ''",
	"texte"	=> "longtext NOT NULL DEFAULT ''",
	"date"	=> "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
	"idx"	=> "tinyint NOT NULL DEFAULT 1",
	"maj" => "TIMESTAMP");
$spip_indexation_key = array(
	"PRIMARY KEY" => "id, type"
	);

$tables_auxiliaires['spip_indexation'] = array(
	'field' => &$spip_indexation,
	'key' => &$spip_indexation_key);

?>
