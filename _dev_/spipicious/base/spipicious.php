<?php

include_spip('base/serial');

global $table_des_tables;
global $tables_principales;

if (!defined("_ECRIRE_INC_VERSION")) return;

$table_des_tables['spipicious'] = 'spipicious';

$spipicious = array(
	"id_mot"	=> "bigint(21) NOT NULL",
	"id_auteur"	=> "bigint(21) NOT NULL",
	"id_article"	=> "bigint(21) NOT NULL",
	"position"	=> "int(10) NOT NULL");
  	
$spipicious_key = array();

$tables_principales['spip_spipicious'] = array(
	'field' => &$spipicious,
	'key' => &$spipicious_key);

?>
