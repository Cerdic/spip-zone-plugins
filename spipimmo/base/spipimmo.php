<?php

	function spipimmo_declarer_tables_interfaces($interface)
	{
		$interface['table_des_tables']['annonces']='annonces';
		$interface['table_des_tables']['documents_annonces']='documents_annonces';
		$interface['table_des_tables']['types_offres']='types_offres';

		return $interface;
	}

	function spipimmo_declarer_tables_principales($tables_principales)
	{
		//Table des annonces
		$spip_annonces_field=array(
			"id_annonce"=>"int(50)",
			"publier"=>"bool",
			"type_offre"=>"varchar(255)",
			"vente_location"=>"varchar(8)",
			"n_mandat"=>"varchar(255)",
			"type_mandat"=>"varchar(50)",
			"date_offre"=>"date",
			"date_modification"=>"date",
			"date_disponibilite"=>"date",
			"negociateur"=>"varchar(255)",
			"prix_loyer"=>"int(30)",
			"honoraires"=>"int(30)",
			"travaux"=>"int(10)",
			"charges"=>"int(10)",
			"depot_garantie"=>"int(10)",
			"taxe_habitation"=>"int(10)",
			"taxe_fonciere"=>"int(10)",
			"adr_bien_1"=>"longtext",
			"adr_bien_2"=>"longtext",
			"cp_bien"=>"varchar(10)",
			"ville_bien"=>"varchar(255)",
			"cp_internet"=>"varchar(10)",
			"ville_internet"=>"varchar(255)",
			"quartier"=>"varchar(255)",
			"residence"=>"varchar(255)",
			"transport"=>"varchar(255)",
			"proximite"=>"varchar(255)",
			"secteur"=>"varchar(255)",
			"categorie"=>"varchar(255)",
			"nb_pieces"=>"int(2)",
			"nb_chambres"=>"int(2)",
			"surf_habit"=>"int(6)",
			"surf_carrez"=>"int(6)",
			"surf_sejour"=>"int(6)",
			"surf_terrain"=>"int(6)",
			"etage"=>"int(2)",
			"code_etage"=>"int(6)",
			"nb_etage"=>"int(2)",
			"annee_cons"=>"varchar(255)",
			"type_cuisine"=>"varchar(255)",
			"nb_wc"=>"int(2)",
			"nb_sdb"=>"int(2)",
			"nb_sde"=>"int(2)",
			"nb_park_int"=>"int(2)",
			"nb_park_ext"=>"int(2)",
			"nb_garages"=>"int(2)",
			"type_soussol"=>"varchar(255)",
			"nb_caves"=>"int(2)",
			"type_chauf"=>"varchar(255)",
			"nat_chauf"=>"varchar(255)",
			"ascenseur"=>"int(2)",
			"balcon"=>"int(4)",
			"terrasse"=>"int(5)",
			"piscine"=>"bool",
			"acces_handi"=>"bool",
			"nb_murs_mit"=>"int(1)",
			"facade_terrain"=>"int(3)",
			"texte_annonce_fr"=>"longtext",
			"texte_annonce_uk"=>"longtext",
			"texte_annonce_sp"=>"longtext",
			"texte_annonce_de"=>"longtext",
			"texte_annonce_it"=>"longtext",
			"texte_mailing"=>"longtext",
			"DPE"=>"varchar(3)",
			"prestige"=>"bool");

		$spip_annonces_key=array(
			"PRIMARY KEY" => "id_annonce");

		$tables_principales['spip_annonces']=array(
			'field' => &$spip_annonces_field,
			'key' => &$spip_annonces_key);


		//Table des documents
		$spip_documents_annonces_field=array(
			"id_document"=>"int",
			"numero_dossier"=>"int(50)",
			"fichier"=>"varchar(255)",
			"taille"=>"int(11)",
			"type"=>"bool");

		$spip_documents_annonces_key=array(
			"PRIMARY KEY" => "id_document");

		$tables_principales['spip_documents_annonces']=array(
			'field' => &$spip_documents_annonces_field,
			'key' => &$spip_documents_annonces_key);


		//Table du type d'offre
		$spip_types_offres_field=array(
			"id_type_offre"=>"int",
			"libelle_offre"=>"varchar(255)");

		$spip_types_offres_key=array(
			"PRIMARY KEY" => "id_type_offre");

		$tables_principales['spip_types_offres']=array(
			'field' => &$spip_types_offres_field,
			'key' => &$spip_types_offres_key);

		return $tables_principales;
	}
?>
