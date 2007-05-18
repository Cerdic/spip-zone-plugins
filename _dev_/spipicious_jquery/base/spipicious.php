<?php

	global $table_des_tables;
	global $tables_principales;
	global $tables_auxiliaires;
	global $tables_jointures;

	$table_des_tables['spipicious'] = 'spipicious';

  $spip_spipicious = array(
  	"id_mot" 	    => "bigint(21) NOT NULL",
  	"id_auteur" 	=> "bigint(21) NOT NULL",
  	"id_article" 	=> "bigint(21) NOT NULL",
    "position"    => "int(10) NOT NULL");
  	
  $spip_spipicious_key = array();

  $tables_principales['spip_spipicious'] = array(
  	'field' => &$spip_spipicious,
  	'key' => &$spip_spipicious_key);

	//
	// <BOUCLE SPIPICIOUS>
	// 
	// argument: id_mot, id_article, id_auteur
	// critere:  position
	function boucle_SPIPICIOUS_dist($id_boucle, &$boucles) {
	        $boucle = &$boucles[$id_boucle];
	        $id_table = $boucle->id_table;
	        $boucle->from[$id_table] =  "spip_spipicious";  
			
	        return calculer_boucle($id_boucle, $boucles); 
	}
	


?>
