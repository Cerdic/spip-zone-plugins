<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function echoppe_tables_principales($tables_principales){
	
	$spip_categories = array(
		"id_categorie"	=> "bigint(21) NOT NULL",
		"id_parent"	=> "bigint(21) NOT NULL",
		"id_secteur" => "bigint(21) NOT NULL",
		"lang"					=> "VARCHAR(2) DEFAULT '' NOT NULL",
		"titre"					=> "text NOT NULL",
		"descriptif"				=> "text NOT NULL",
		"texte"					=> "longblob NOT NULL",
		"logo"					=> "text NOT NULL",
		"maj"					=> "TIMESTAMP",
		"statut"				=> "VARCHAR(10) DEFAULT '0' NOT NULL"
		);
	
	$spip_categories_key = array(
		"PRIMARY KEY"		=> "id_categorie",
		"KEY id_parent"	=> "id_parent",
		"KEY id_secteur" => "id_secteur",
		"KEY lang"		=> "lang",
		"KEY statut"		=> "statut"
		);

	$spip_categories_join = array(
		"id_categorie"		=> "id_categorie",
		"id_parent"		=> "id_parent",
		"id_secteur" => "id_secteur",
		"lang"			=>"lang"
		);
		
	$tables_principales['spip_echoppe_categories'] = array(
		'field' => &$spip_categories,
		'key' => &$spip_categories_key,
		'join' => &$spip_categories_join
	);
	
	
	$spip_produits = array(
		"id_produit"		=> "bigint(21) NOT NULL",
		"id_categorie"		=> "bigint(21) DEFAULT '0' NOT NULL",
		"date_debut"		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_fin"		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"poids"			=> "float DEFAULT '0' NOT NULL", 	// (crowfoot : probablement pas internationalisé pour le calcul des frais de port)
		"hauteur"		=> "float DEFAULT '0' NOT NULL", 	// (crowfoot : serait important pour le calcul des frais de livraison ...)
		"largeur"		=> "float DEFAULT '0' NOT NULL",
		"longueur"		=> "float DEFAULT '0' NOT NULL",
		"colisage"		=> "VARCHAR(10) DEFAULT '' NOT NULL", //(ça pourrait etre une donnée d'info sur la taille du colis ( ou lettre, ou palettes ... etc )
		"ref_produit"		=> "VARCHAR(255) NOT NULL",
		"prix_base_htva"	=> "float DEFAULT '0' NOT NULL", 	//(permettrait de donner un prix de base et pas faire 50000 jointures pour retrouver le prix dans une liste de produits....)
		"maj"			=> "TIMESTAMP",
		"statut"			=> "VARCHAR(10) DEFAULT '0' NOT NULL",
		"lang"				=> "VARCHAR(2) DEFAULT '' NOT NULL",
		"titre"				=> "text NOT NULL", 
		"descriptif"		=> "text NOT NULL",
		"texte"				=> "longblob NOT NULL",
		"ps"				=> "text NOT NULL",
		"tva"				=> "float DEFAULT '0' NOT NULL",	// (doit à mon avis être internationalisé... non ? )
		"quantite_mini"		=> "int NOT NULL",
		"logo"				=> "text NOT NULL",
		"maj"				=> "TIMESTAMP",
		);
	
	$spip_produits_key = array(
		"PRIMARY KEY"		=> "id_produit",
		"KEY id_categorie"		=> "id_categorie",
		"KEY statut"		=> "statut",
		"KEY date_debut"	=> "date_debut",
		"KEY date_fin"		=> "date_fin",
		"KEY ref_produit"	=> "ref_produit",
		"KEY lang"			=> "lang"
		);

	$spip_produits_join = array(
		"id_produit"	=> "id_produit",
		"id_categorie"	=> "id_categorie",
		"statut"	=> "statut",
		"date_debut"	=> "date_debut",
		"date_fin"	=> "date_fin",
		"lang"			=> "lang"
		);
	
	$tables_principales['spip_echoppe_produits'] = array(
		'field' => &$spip_produits,
		'key' => &$spip_produits_key,
		'join' => &$spip_produits_join
	);
	
	$spip_stocks = array(
		"id_stock"			=> "bigint(21) NOT NULL",
		"ref_produit"		=> "bigint(21) NOT NULL",
		"configuration"		=> "longblob NOT NULL", // Utile si on veux renseigner qu'il y a 3 graveur DVD-425RW face noir+BurnProof et 10 graveur DVD-425RW face blanche+BurnProof
		"id_depot"		=> "bigint(21) NOT NULL",
		"quantite"		=> "int NOT NULL",
		"maj"			=> "TIMESTAMP"
		);

	$spip_stocks_key = array(
		"PRIMARY KEY"		=> "id_stock",
		"KEY ref_produit"	=> "ref_produit",
		"KEY id_depot"		=>"id_depot"
		);

	$spip_stocks_join = array(
		"ref_produit"	=> "ref_produit",
		"id_depot"	=>"id_depot"
		);
	
	$tables_principales['spip_echoppe_stocks'] = array(
		'field' => &$spip_stocks,
		'key' => &$spip_stocks_key,
		'join' => &$spip_stocks_join
	);
	
	$spip_depots = array(
		"id_depot"			=> "bigint(21) NOT NULL",
		"titre"				=> "tinytext NOT NULL",
		"descriptif"			=> "text NOT NULL",
		"adresse"				=> "tinytext NOT NULL",
		"maj"				=> "TIMESTAMP"
		);
	
	$spip_depots_key = array(
		"PRIMARY KEY"			=> "id_depot"
		);

	$spip_depots_join = array(
		"id_depot"			=> "id_depot"
		);	
	
	$tables_principales['spip_echoppe_depots'] = array(
		'field' => &$spip_depots,
		'key' => &$spip_depots_key,
		'join' => &$spip_depots_join
	);
	
	$spip_gammes = array(
		"id_gamme"			=> "bigint(21) NOT NULL",
		"titre"				=> "text NOT NULL", // On se pete pas la tete, on utilise les multi
		"descriptif"		=> "text NOT NULL"
		);

	$spip_gammes_key = array(
		"PRIMARY KEY"			=> "id_gamme"
		);

	$spip_gammes_join = array(
		"id_gamme"			=> "id_gamme"
		);	
	
	$tables_principales['spip_echoppe_gammes'] = array(
		'field' => &$spip_gammes,
		'key' => &$spip_gammes_key,
		'join' => &$spip_gammes_join
	);
	
	$spip_options = array(
		"id_option"	=> "bigint(21) NOT NULL",
		"id_produit"	=> "bigint(21) DEFAULT '0' NOT NULL",//( si =0 et id_categorie != 0 c'est donc une option sur une categorie ) crowfoot +1
		"id_categorie"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"texte"			=> "text NOT NULL",
		"lang"			=> "VARCHAR(2) DEFAULT '' NOT NULL"
		);

	$spip_options_key = array(
		"PRIMARY KEY"		=> "id_option",
		"KEY id_produit"	=> "id_produit",//( si =0 et id_categorie != 0 c'est donc une option sur une categorie ) crowfoot +1
		"KEY id_categorie"	=> "id_categorie",
		"KEY lang"	=> "lang"
		);

	$spip_options_join = array(
		"id_option"	=> "id_option",
		"id_produit"	=> "id_produit",//( si =0 et id_categorie != 0 c'est donc une option sur une categorie ) crowfoot +1
		"id_categorie"	=> "id_categorie",
		"lang"				=> "lang"
		);

	$tables_principales['spip_echoppe_options'] = array(
		'field' => &$spip_options,
		'key' => &$spip_options_key,
		'join' => &$spip_options_join
	);
	
	$spip_valeurs = array(
		"id_valeur"		=> "bigint(21) NOT NULL",
		"id_option"			=> "bigint(21) NOT NULL",
		"valeur"			=> "text NOT NULL",
		"defaut"			=> "bool NOT NULL",
	//	"lang"				=> "VARCHAR(2) DEFAULT '' NOT NULL", //crowfoot : si langue ne vaut rien (ou 0), alors la valeur est accessible dans toutes les langues.( yoann : bonne idée)
		"texte"				=> "text NOT NULL" //( pour une option couleur, on aura ici « rouge, vert » etc )
		);

	$spip_valeurs_key = array(
		"PRIMARY KEY"		=> "id_valeur",
		"KEY id_option"		=> "id_option",
		"KEY defaut"		=> "defaut"
		);


	$spip_valeurs_join = array(
		"id_valeur"		=> "id_valeur",
		"id_option"			=> "id_option",
		"defaut"			=> "defaut"
		);

	$tables_principales['spip_echoppe_valeurs'] = array(
		'field' => &$spip_valeurs,
		'key' => &$spip_valeurs_key,
		'join' => &$spip_valeurs_join
	);
	
	$spip_prix = array(
		"id_prix"			=> "bigint(21) NOT NULL",
		"id_produit"			=> "bigint(21) NOT NULL",
		"configuration"			=> "longblob NOT NULL",
		"prix"				=> "float NOT NULL",
		"date_debut"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_fin"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL"
		);

	$spip_prix_key = array(
		"PRIMARY KEY"			=> "id_prix",
		"KEY id_produit"		=> "id_produit",
		"KEY date_debut"		=> "date_debut",
		"KEY date_fin"			=> "date_fin"
		);

	$spip_prix_join = array(
		"id_prix"			=> "id_prix",
		"id_produit"			=> "id_produit",
		"date_debut"			=> "date_debut",
		"date_fin"			=> "date_fin"
		);

	$tables_principales['spip_echoppe_prix'] = array(
		'field' => &$spip_prix,
		'key' => &$spip_prix_key,
		'join' => &$spip_prix_join
	);
	
	$spip_clients = array(
		"id_client"	=> "bigint(21) NOT NULL",
		"id_auteur"	=> "bigint(21) NOT NULL",
		"token_client"	=> "VARCHAR(40) NOT NULL"
		);


	$spip_clients_key = array(
		"PRIMARY KEY"		=> "id_client",
		"KEY id_auteur"		=> "id_auteur",
		"KEY token_client"	=> "token_client"
		);


	$spip_clients_join = array(
		"id_client"	=> "id_client",
		"id_auteur"	=> "id_auteur",
		"token_client"	=> "token_client"
		);

	$tables_principales['spip_echoppe_clients'] = array(
		'field' => &$spip_clients,
		'key' => &$spip_clients_key,
		'join' => &$spip_clients_join
	);
	
	$spip_paniers = array(
		"id_panier"	=> "bigint(21) NOT NULL", //Un panier complet est constitue de plusieurs enregistrement de cette table. Tous relies par token_panier
		"id_client"	=> "bigint(21) NOT NULL",
		"id_produit"	=> "bigint(21) NOT NULL",
		"quantite"	=> "bigint(21) NOT NULL",
		"configuration"	=> "longblob NOT NULL",
		"token_client"	=> "VARCHAR(40) NOT NULL",
		"token_panier"	=> "VARCHAR(40) NOT NULL",
		"statut"		=> "VARCHAR(10) NOT NULL",
		"date"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL"
		);


	$spip_paniers_key = array(
		"PRIMARY KEY"		=> "id_panier",
		"KEY id_client"		=> "id_client",
		"KEY token_client"	=> "token_client",
		"KEY token_panier"	=> "token_panier"
		);


	$spip_paniers_join = array(
		"id_panier"	=> "id_panier",
		"id_client"	=> "id_client",
		"token_client"	=> "token_client",
		"token_panier"	=> "token_panier"
		);

	$tables_principales['spip_echoppe_clients'] = array(
		'field' => &$spip_clients,
		'key' => &$spip_clients_key,
		'join' => &$spip_clients_join
	);
	
	$spip_commentaires_paniers = array(
		"id_commentaire_panier"	=> "bigint(21) NOT NULL",
		"token_panier"	=> "VARCHAR(255) NOT NULL",
		"statut"		=> "VARCHAR(10) NOT NULL",
		"texte"	=> "TINYTEXT NOT NULL",
		"date"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL"
		);

	$spip_commentaires_paniers_key = array(
		"PRIMARY KEY"		=> "id_commentaire_panier",
		"KEY token_panier"	=> "token_panier",
		"KEY statut"		=> "statut"
		);

	$spip_commentaires_paniers_join = array(
		"token_panier"	=> "token_panier"
		);

	$tables_principales['spip_echoppe_commentaires_paniers'] = array(
		'field' => &$spip_commentaires_paniers,
		'key' => &$spip_commentaires_paniers_key,
		'join' => &$spip_commentaires_paniers_join
	);
	
	$spip_prestataires = array(
		"id_prestataire"	=> "bigint(21) NOT NULL",
		"titre"	=> "VARCHAR(255) NOT NULL",
		"texte"	=> "MEDIUMTEXT NOT NULL",
		"modele"			=> "TINYTEXT NOT NULL",
		"type"			=> "ENUM('paiement','livraison') DEFAULT 'paiement' NOT NULL",
		"statut"		=> "VARCHAR(10) NOT NULL",
		);

	$spip_prestataires_key = array(
		"PRIMARY KEY"		=> "id_prestataire",
		"KEY type"	=> "type"
		);

	$spip_prestataires_join = array(
		"id_prestataire"		=> "id_prestataire",
		"modele"				=>"modele",
		"type"					=> "type"
		);
	
	$tables_principales['spip_echoppe_prestataires'] = array(
		'field' => &$spip_prestataires,
		'key' => &$spip_prestataires_key,
		'join' => &$spip_prestataires_join
	);
	
	return $tables_principales;
	
	
}

?>
