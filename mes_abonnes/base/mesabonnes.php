<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

	global $table_des_tables;
	global $tables_principales;
	global $tables_auxiliaires;
	global $tables_jointures;

	$spip_mesabonnes = array(
	  	"id_abonne" 	    => "bigint(21) NOT NULL auto_increment",  
      "nom"             => "text DEFAULT '' NOT NULL",         
	  	"email"           => "text DEFAULT '' NOT NULL",
	  	"lang"            => "tinytext DEFAULT '' NOT NULL",
	  	"date_modif"	    => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
	  	"liste"	          => "text DEFAULT '' NOT NULL",              // pas utilise pour l'instant, gestion multi-liste ?
	  	"statut"	        => "varchar(6)  DEFAULT '0' NOT NULL");
  
 	$spip_mesabonnes_key = array(
		"PRIMARY KEY" 	=> "id_abonne");

 	$tables_principales['spip_mesabonnes'] = array(
	  	'field' => &$spip_mesabonnes,
	  	'key' => &$spip_mesabonnes_key);

	$table_des_tables['mesabonnes'] = 'mesabonnes';
	

?>