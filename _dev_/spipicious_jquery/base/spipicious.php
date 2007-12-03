<?php
/*
 * spipicious
 * Gestion de tags lies aux auteurs
 *
 * Auteurs :
 * Quentin Drouet
 * Erational
 * 
 * 2007 - Distribue sous licence GNU/GPL
 *
 */
 
	global $table_des_tables;
	global $tables_principales;
	global $tables_auxiliaires;
	global $tables_jointures;

	$spip_spipicious = array(
	  	"id_mot" 	    => "bigint(21) NOT NULL",
	  	"id_auteur" 	=> "bigint(21) NOT NULL",
	  	"id_article" 	=> "bigint(21) NOT NULL",
		"id_rubrique" 	=> "bigint(21) NOT NULL",
		"id_document" 	=> "bigint(21) NOT NULL",
	    "position"    => "int(10) NOT NULL",
		"maj" => "TIMESTAMP");
  
 	$spip_spipicious_key = array(
		"PRIMARY KEY" 	=> "id_mot",
		"KEY id_auteur" => "id_auteur");

 	$tables_principales['spip_spipicious'] = array(
	  	'field' => &$spip_spipicious,
	  	'key' => &$spip_spipicious_key);
	
//	$tables_jointures['spip_mots'][] = 'spipicious';

	$table_des_tables['spipicious'] = 'spipicious';

?>
