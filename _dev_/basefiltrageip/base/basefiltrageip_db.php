<?php

$GLOBALS['basefiltrageip_version'] = 2;

$spip_basefiltrageip_whitelist = array(
	"first_ip" 	=> "long NOT NULL",
	"last_ip" 	=> "long NOT NULL",
	"maj" 		=> "TIMESTAMP");

$spip_basefiltrageip_whitelist_key = array(
						 "PRIMARY KEY" => "first_ip",
						 "UNIQ" => "last_ip");

global $tables_principales;
$tables_principales['spip_basefiltreageip_whitelist'] = array(
	'field' => &$spip_basefiltrageip_whitelist,
	'key' => &$spip_basefiltrageip_whitelist_key);


$spip_basefiltrageip_log = array(
								 "ip" 	=> "long NOT NULL",
								 "reason_id" => "int NOT NULL",
								 "reason_explanation" => "text DEFAULT ''",
								 "count" => "bigint DEFAULT 0 NOT NULL",
								 "firstseen" 		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
								 "lastseen" 		=> "TIMESTAMP");

$spip_basefiltrageip_log_key = array(
						 "KEY" => "ip",
						 "KEY" => "reason_id");

$tables_principales['spip_basefiltreageip_log'] = array(
	'field' => &$spip_basefiltrageip_log,
	'key' => &$spip_basefiltrageip_log_key);


$spip_basefiltrageip_reasons = array(
									 "reason_id" => "int NOT NULL",
									 "reason_name" => "text");

$spip_basefiltrageip_reasons_key = array(
						 "PRIMARY KEY" => "reason_id");

$tables_principales['spip_basefiltreageip_reasons'] = array(
	'field' => &$spip_basefiltrageip_reasons,
	'key' => &$spip_basefiltrageip_reasons_key);



//-- Relations ----------------------------------------------------
global $tables_jointures;
$tables_jointures['spip_basefiltreageip_reasons'][] = 'spip_basefiltreageip_log';

global $table_des_tables;
$table_des_tables['basefiltrageip_whitelist']='basefiltrageip_whitelist';
$table_des_tables['basefiltrageip_log']='basefiltrageip_log';



?>
