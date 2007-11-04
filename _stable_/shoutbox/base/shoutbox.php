<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

global $tables_principales;

$spip_shoutbox = array(
	"id_shoutbox" => "bigint(21) NOT NULL",
	"objet"	=> "VARCHAR(25) NOT NULL DEFAULT ''",  # 'article12', '' pour le site
	"id_auteur"	=> "bigint(21)",    # qui a poste ? NULL si pas auteur
	"auteur"	=> "text NOT NULL DEFAULT ''",  # nom ou IP
	"texte"	=> "longtext NOT NULL DEFAULT ''",
	"date"	=> "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
	"maj" => "TIMESTAMP");
$spip_shoutbox_key = array(
	"PRIMARY KEY" => "id_shoutbox",
	"KEY objet" => "objet (25)",
	"KEY id_auteur" => "id_auteur"
	);

$tables_principales['spip_shoutbox'] = array(
	'field' => &$spip_shoutbox,
	'key' => &$spip_shoutbox_key);

?>
