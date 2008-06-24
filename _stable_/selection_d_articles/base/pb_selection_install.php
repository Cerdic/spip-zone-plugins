<?

//
// Formulaires : Structure
//


	function pb_selection_upgrade() {
		global $tables_principales;
	
		$version_base = 0.3;
		$current_version = 0.0;
		

		include_spip('base/serial');

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
	
			include_spip('base/create');
			include_spip('inc/metas');
//			echo "CREER";			
			creer_base();
			ecrire_meta("pb_selection_version",$version_base,'non');
			ecrire_metas();
	
	}

/*
	function pb_selection_vider_tables($nom_meta_base_version) {
		spip_query("DROP TABLE spip_pb_selection");
		effacer_meta("pb_selection_version");
		ecrire_metas();
	}
*/

?>