<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
//
// Formulaires : Structure
//

function gestion_projets_declarer_tables_principales($tables_principales){
	$spip_projets = array(
		"id_projet" 		=> "int(21) NOT NULL",
		"id_parent" 		=> "int(21) NOT NULL",	
		"id_auteur" 		=> "int(21) NOT NULL",				
		"nom" 			=> "varchar(255) NOT NULL",
		"date_creation" 	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",		
		"maj" 			=> "TIMESTAMP");
	
	$spip_projets_key = array(
		"PRIMARY KEY" 	=> "id_projet",
		"KEY id_parent"	=> "id_parent",
		"KEY id_auteur"	=> "id_auteur",		
		);
		
	$spip_projets_join = array(
		"id_projet"	=> "id_projet",
		"id_auteur"	=> "id_auteur",		
		);

	$tables_principales['spip_projets'] = array(
		'field' => &$spip_projets,
		'key' => &$spip_projets_key,
		'join' => &$spip_projets_join
	);
		
	return $tables_principales;
	
	}
?>
