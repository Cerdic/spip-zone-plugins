<?php

function  partageur_declarer_tables_interfaces($interface){
   // 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['partageurs']='partageurs';
	
	return $interface;
}



function partageur_declarer_tables_principales($tables_principales){
  //-- Table PARTAGEURS ------------------------------------------
	$spip_partageur = array(
	  	"id_partageur"  => "bigint(21) NOT NULL auto_increment",
	  	"titre"  	      => "text NOT NULL",
	  	"url_site" 	    => "text NOT NULL",
	  	//"url_syndic" 	  => "text NOT NULL",
		  "maj" 	        => "TIMESTAMP"
  );
	$spip_partageur_key = array("PRIMARY KEY" 	=> "id_partageur");	
  
  $tables_principales['spip_partageurs'] = array(
	  	'field' => &$spip_partageur,
	  	'key' => &$spip_partageur_key); 

  return $tables_principales;
}

?>