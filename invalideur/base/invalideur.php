<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

global $tables_auxiliaires;
global $tables_principales;
global $tables_jointures;

$spip_caches = array(
		"fichier" => "char (64) NOT NULL",
		"id" => "char (64) NOT NULL",
		// i=par id, t=timer, x=suppression
		"type" => "CHAR (1) DEFAULT 'i' NOT NULL",
		"taille" => "integer DEFAULT '0' NOT NULL");
$spip_caches_key = array(
		"PRIMARY KEY"	=> "fichier, id",
		"KEY fichier" => "fichier",
		"KEY id" => "id");

$tables_auxiliaires['spip_caches'] = array(
	'field' => &$spip_caches,
	'key' => &$spip_caches_key);

?>