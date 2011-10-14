<?php

//
// Formulaires : Structure
//
if (!defined("_ECRIRE_INC_VERSION")) return;

function pb_selection_declarer_tables_principales($tables_principales){
	

	$spip_pb_selection = array(
		"id_rubrique" 	=> "bigint(21) NOT NULL",
		"id_article" 	=> "bigint(21) NOT NULL",
		"ordre" 	=> "bigint(21) NOT NULL",
		"maj" 		=> "TIMESTAMP");
	
	$spip_pb_selection_key = array(
		"PRIMARY KEY" 	=> "id_rubrique, id_article"
		);
	
	$tables_principales['spip_pb_selection'] = array(
		'field' => &$spip_pb_selection,
		'key' => &$spip_pb_selection_key);
	return $tables_principales;
	
	}


?>
