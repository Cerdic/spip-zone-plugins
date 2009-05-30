<?

//
// Formulaires : Structure
//


	function pb_pagerank_upgrade() {
		global $tables_principales;

	/*	
		$version_base = 0.8;

		$current_version = 0.0;

		if (   (isset($GLOBALS['meta']["pb_panier_version"]) )
				&& ( ($current_version = $GLOBALS['meta']["pb_panier_version"]) == $version_base) ) {
			return;		
		}
					
		$spip_pb_produit = array(
			"id_article" 	=> "bigint(21) NOT NULL",
			"reference" 	=> "text",
			"code_barre" 	=> "text",
			"prix_ht" 		=> "float(20,2) NOT NULL",
			"prix_ttc" 		=> "float(20,2) NOT NULL",
			"tva" 			=> "float(6,2) NOT NULL",
			"largeur" 		=> "bigint(21)  NOT NULL",
			"hauteur"	 	=> "bigint(21)  NOT NULL",
			"profondeur" 	=> "bigint(21)  NOT NULL",
			"poids" 		=> "bigint(21)  NOT NULL",
			"stock" 		=> "bigint(21) NOT NULL",
			"maj" 			=> "TIMESTAMP");
		
		$spip_pb_produit_key = array(
			"PRIMARY KEY" 	=> "id_article"
			);
		
		$tables_principales['spip_pb_produits'] = array(
			'field' => &$spip_pb_produit,
			'key' => &$spip_pb_produit_key);
	
			
		$spip_pb_commandes = array(
			"id_commande" 	=> "bigint(21) NOT NULL",
			"id_auteur" 	=> "bigint(21) NOT NULL",
			"adresse1" 	=> "text",
			"adresse2" 	=> "text",
			"adresse_code_postal" 	=> "text",
			"adresse_ville" => "text",
			"adresse_pays" 	=> "text",
			"commande"	 	=> "text",
			"total_paye" 		=> "float(20,2)",
			"statut" 		=> "varchar(5) NOT NULL",
			"date" 			=> "TIMESTAMP");
		
		$spip_pb_commandes_key = array(
			"PRIMARY KEY" 	=> "id_commande"
			);
		
		$tables_principales['spip_pb_commandes'] = array(
			'field' => &$spip_pb_commandes,
			'key' => &$spip_pb_commandes_key);
	

		if ($current_version==0.0 OR 1==1){
			include_spip('base/create');
			include_spip('inc/metas');
			creer_base();
			ecrire_meta("pb_panier_version",$version_base,'non');
			ecrire_metas();
		}
		else return;
	*/
	}

/*
	function pb_selection_vider_tables($nom_meta_base_version) {
		spip_query("DROP TABLE spip_pb_selection");
		effacer_meta("pb_selection_version");
		ecrire_metas();
	}
*/

?>