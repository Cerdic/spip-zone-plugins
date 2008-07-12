<?php
/*
 * spip2spip
 *
 * Auteurs :
 * Erational
 * 
 * 2008 - Distribue sous licence GNU/GPL
 *
 */
 
	global $table_des_tables;
	global $tables_principales;
	global $tables_auxiliaires;
	global $tables_jointures;

	$spip_spip2spip = array(
	  	"id" 	    => "int(5) NOT NULL auto_increment",
	  	"site_titre" 	=> "varchar(254) NOT NULL default ''",
	  	"site_rss" 	=> "varchar(254) NOT NULL default ''",
		"last_syndic" 	=> "TIMESTAMP");
  
 	$spip_spip2spip_key = array(
		"PRIMARY KEY" 	=> "id");

 	$tables_principales['spip_spip2spip'] = array(
	  	'field' => &$spip_spip2spip,
	  	'key' => &$spip_spip2spip_key);

	$table_des_tables['spip2spip'] = 'spip2spip';

?>