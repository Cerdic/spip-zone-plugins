<?php

//
// Formulaires : Structure
//

function pb_selection_declarer_tables_principales($tables_principales){

		global $tables_principales;
	
		$version_base = 0.3;
		$current_version = 0.0;
		

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

/*
	function pb_selection_vider_tables($nom_meta_base_version) {
		spip_query("DROP TABLE spip_pb_selection");
		effacer_meta("pb_selection_version");
		ecrire_metas();
	}
*/

?>
