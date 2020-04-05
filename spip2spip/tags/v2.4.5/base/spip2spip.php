<?php
/**
 * Plugin spip2spip pour Spip 2.0
 * Licence GPL
 * 
 *
 */
 

/* 
pas utile pour l'instant
function  spip2spip_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['spip2spip']='spip2spip';
	
	return $interface;
}
*/


function spip2spip_declarer_tables_principales($tables_principales){
  //-- Table SPIP2SPIP ------------------------------------------
	$spip_spip2spip = array(
	  	"id" 	    => "bigint(21) NOT NULL auto_increment",
	  	"site_titre" 	=> "varchar(255) NOT NULL default ''",
	  	"site_rss" 	=> "varchar(255) NOT NULL default ''",
		  "last_syndic" 	=> "TIMESTAMP");
	$spip_spip2spip_key = array("PRIMARY KEY" 	=> "id");	
  
  $tables_principales['spip_spip2spip'] = array(
	  	'field' => &$spip_spip2spip,
	  	'key' => &$spip_spip2spip_key); 
   

	$tables_principales['spip_articles']['field']['s2s_url'] = "VARCHAR(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_articles']['field']['s2s_url_trad'] = "VARCHAR(255) DEFAULT '' NOT NULL";

  return $tables_principales;
}



?>