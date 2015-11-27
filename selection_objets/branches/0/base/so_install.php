<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
//
// Formulaires : Structure
//

function so_declarer_tables_principales($tables_principales){
	$spip_selection_objets = array(
		"id_objet" 	=> "bigint(21) NOT NULL",
		"id_objet_dest" => "bigint(21) NOT NULL",		
		"objet" 	=> "varchar(100) NOT NULL",
		"objet_dest" 	=> "varchar(100) NOT NULL",				
		"ordre" 	=> "bigint(21) NOT NULL",
		"lang" 	=> "varchar(10) NOT NULL",		
		"maj" 		=> "TIMESTAMP");
	
	$spip_selection_objets_key = array(
		"KEY id_objet" 	=> "id_objet"
		);
	
	$tables_principales['spip_selection_objets'] = array(
		'field' => &$spip_selection_objets,
		'key' => &$spip_selection_objets_key);
		
	return $tables_principales;
	
	}
?>
