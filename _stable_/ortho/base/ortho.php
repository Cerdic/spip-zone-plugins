<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

global $tables_auxiliaires;

$spip_ortho_cache = array(
	"lang" => "VARCHAR(10) NOT NULL",
	"mot" => "VARCHAR(255) BINARY NOT NULL",
	"ok" => "TINYINT",
	"suggest" => "TEXT",
	"maj" => "TIMESTAMP");
$spip_ortho_cache_key = array(
	"PRIMARY KEY" => "lang, mot",
	"KEY maj" => "maj");

$spip_ortho_dico = array(
	"lang" => "VARCHAR(10) NOT NULL",
	"mot" => "VARCHAR(255) BINARY NOT NULL",
	"id_auteur" => "BIGINT UNSIGNED",
	"maj" => "TIMESTAMP");
$spip_ortho_dico_key = array(
	"PRIMARY KEY" => "lang, mot");


$tables_auxiliaires['spip_ortho_cache'] = array(
	'field' => &$spip_ortho_cache,
	'key' => &$spip_ortho_cache_key);
$tables_auxiliaires['spip_ortho_dico'] = array(
	'field' => &$spip_ortho_dico,
	'key' => &$spip_ortho_dico_key);

?>