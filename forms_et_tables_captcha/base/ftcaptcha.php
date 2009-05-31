<?php
global $tables_principales;
global $tables_auxiliaires;

$spip_captcha = array(
"captcha_id" => "int(11) unsigned NOT NULL auto_increment",
"captcha_code" => "varchar(32) NOT NULL default ''",
"captcha_solution" => "varchar(32) NOT NULL default ''",
"captcha_time" => "int(10) unsigned default NULL",
"captcha_ip_address" => "varchar(16) default '0'",
"captcha_user_agent" => "varchar(100) default ''",
);
					
$spip_captcha_key = array(
"PRIMARY KEY" => "captcha_id");

global $table_des_tables;
$table_des_tables['captcha'] = 'captcha';

$tables_principales['spip_captcha'] =
array('field' => &$spip_captcha, 'key' => &$spip_captcha_key);
?>