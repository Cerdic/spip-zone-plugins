<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
	
//include_spip('base/serial');

global $tables_principales;
global $tables_auxiliaires;
global $tables_jointures;
global $table_des_tables;

$GLOBALS['echoppe_version_base'] = 0.5;


$spip_echoppe_categories = array(
	"id_categorie"	=> "bigint(21) NOT NULL",
	"id_parent"	=> "bigint(21) NOT NULL"
	);
	
$spip_echoppe_categories_key = array(
	"PRIMARY KEY"		=> "id_categorie",
	"KEY id_parent"	=> "id_parent"
	);

$spip_echoppe_categories_join = array(
	"id_categorie"		=> "id_categorie",
	"id_parent"		=> "id_parent"
	);


$spip_echoppe_categories_descriptions = array(
	"id_categorie_description"	=> "bigint(21) NOT NULL",
	"id_categorie"				=> "bigint(21) NOT NULL",
	"lang"					=> "VARCHAR(2) DEFAULT '' NOT NULL",
	"titre"					=> "text NOT NULL",
	"descriptif"				=> "text NOT NULL",
	"texte"					=> "longblob NOT NULL",
	"logo"					=> "text NOT NULL",
	"maj"					=> "TIMESTAMP",
	"statut"				=> "VARCHAR(10) DEFAULT '0' NOT NULL"	// ( je le mets ici, comme �a, on peut avoir moins de cat�gories dans les langues �trang�res)
	);

$spip_echoppe_categories_descriptions_key = array(
	"PRIMARY KEY"		=> "id_categorie_description",
	"KEY id_categorie"    => "id_categorie",
	"KEY lang"		=> "lang",
	"KEY statut"		=> "statut"
	);

$spip_echoppe_categories_descriptions_join = array(
	"id_categorie"		=>"id_categorie",
	"lang"			=>"lang",
	);

$spip_echoppe_produits = array(
	"id_produit"		=> "bigint(21) NOT NULL",
	"date_debut"		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
	"date_fin"		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
	"poids"			=> "float DEFAULT '0' NOT NULL", 	// (crowfoot : probablement pas internationalis� pour le calcul des frais de port)
	"hauteur"		=> "float DEFAULT '0' NOT NULL", 	// (crowfoot : serait important pour le calcul des frais de livraison ...)
	"largeur"		=> "float DEFAULT '0' NOT NULL",
	"longueur"		=> "float DEFAULT '0' NOT NULL",
	"colisage"		=> "VARCHAR(10) DEFAULT '' NOT NULL", //(�a pourrait etre une donn�e d'info sur la taille du colis ( ou lettre, ou palettes ... etc )
	"ref_produit"		=> "VARCHAR(255) NOT NULL",
	"prix_base_htva"	=> "float DEFAULT '0' NOT NULL", 	//(permettrait de donner un prix de base et pas faire 50000 jointures pour retrouver le prix dans une liste de produits....)
	"maj"			=> "TIMESTAMP",
	"statut"		=> "VARCHAR(10) DEFAULT '0' NOT NULL"
	);
	
$spip_echoppe_produits_key = array(
	"PRIMARY KEY"		=> "id_produit",
	"KEY statut"		=> "statut",
	"KEY date_debut"	=> "date_debut",
	"KEY date_fin"		=> "date_fin",
	"KEY ref_produit"	=> "ref_produit"
	);

$spip_echoppe_produits_join = array(
	"id_produit"	=> "id_produit",
	"statut"	=> "statut",
	"date_debut"	=> "date_debut",
	"date_fin"	=> "date_fin"
	);

$spip_echoppe_produits_descriptions = array(
	"id_descriptif_produit"	=> "bigint(21) NOT NULL",
	"id_produit"			=> "bigint(21) NOT NULL",
	"lang"				=> "VARCHAR(2) DEFAULT '' NOT NULL",
	"titre"				=> "text NOT NULL", 
	"descriptif"			=> "text NOT NULL",
	"texte"				=> "longblob NOT NULL",
	"ps"				=> "text NOT NULL",
	"tva"				=> "float DEFAULT '0' NOT NULL",	// (doit � mon avis �tre internationalis�... non ? )
	"quantite_mini"			=> "int NOT NULL",
	"logo"				=> "text NOT NULL",
	"maj"				=> "TIMESTAMP",
	);

$spip_echoppe_produits_descriptions_key = array(
	"PRIMARY KEY"			=> "id_descriptif_produit",
	"KEY id_produit"		=> "id_produit",
	"KEY lang"			=> "lang"
	);

$spip_echoppe_produits_descriptions_join = array(
	"id_produit"		=> "id_produit",
	"lang"			=> "lang"
	);


$spip_echoppe_stock_produits = array(
	"id_stock"			=> "bigint(21) NOT NULL",
	"id_produit"		=> "bigint(21) NOT NULL",
	"configuration"		=> "blob NOT NULL", // Utile si on veux renseigner qu'il y a 3 graveur DVD-425RW face noir+BurnProof et 10 graveur DVD-425RW face blanche+BurnProof
	"id_depot"		=> "bigint(21) NOT NULL",
	"quantite"		=> "int NOT NULL",
	"maj"			=> "TIMESTAMP"
	);

$spip_echoppe_stock_produits_key = array(
	"PRIMARY KEY"		=> "id_stock",
	"KEY id_produit"	=> "id_produit",
	"KEY id_depot"		=>"id_depot"
	);

$spip_echoppe_stock_produits_join = array(
	"id_produit"	=> "id_produit",
	"id_depot"	=>"id_depot"
	);

$spip_echoppe_depots = array(
	"id_depot"			=> "bigint(21) NOT NULL",
	"titre"				=> "tinytext NOT NULL",
	"descriptif"			=> "text NOT NULL",
	"adresse"				=> "tinytext NOT NULL",
	"maj"				=> "TIMESTAMP"
	);

$spip_echoppe_depots_key = array(
	"PRIMARY KEY"			=> "id_depot"
	);

$spip_echoppe_depots_join = array(
	"id_depot"			=> "id_depot"
	);

$spip_echoppe_categories_produits = array(
	"id_categorie"			=> "bigint(21) NOT NULL",
	"id_produit"			=> "bigint(21) NOT NULL"
	);

$spip_echoppe_categories_produits_key = array(
	"PRIMARY KEY"			=> "id_categorie, id_produit",
	"KEY id_produit"		=> "id_produit"
	);

$spip_echoppe_categories_produits_join = array(
	"id_categorie"			=> "id_categorie",
	"id_produit"			=> "id_produit"
	);



// Un produits peut faire partie d'une cat�gorie : �cran smasung, mais aussi d'une gamme : "SyncMaster" ou "Ordinateur complet" 
// A voir si ca fait pas double emplois avec les mots cl�s
$spip_echoppe_gammes = array(
	"id_gamme"			=> "bigint(21) NOT NULL",
	"titre"				=> "text NOT NULL", // On se pete pas la tete, on utilise les multi
	"descriptif"			=> "text NOT NULL"
	);

$spip_echoppe_gammes_key = array(
	"PRIMARY KEY"			=> "id_gamme"
	);

$spip_echoppe_gammes_join = array(
	"id_gamme"			=> "id_gamme"
	);

$spip_echoppe_gammes_produits = array(
	"id_gamme"			=> "bigint(21) NOT NULL",
	"id_produit"			=> "bigint(21) NOT NULL"
	);

$spip_echoppe_gammes_produits_key = array(
	"PRIMARY KEY"			=> "id_gamme",
	"KEY id_produit"		=> "id_produit"
	);

$spip_echoppe_gammes_produits_join = array(
	"id_gamme"			=> "id_gamme",
	"id_produit"			=> "id_produit"
	);


//
// Base des liens avec les objets spip
//
$spip_echoppe_categories_rubriques = array(
	"id_categorie"			=> "bigint(21) NOT NULL",
	"id_rubrique"			=> "bigint(21) NOT NULL"
	);

$spip_echoppe_categories_rubriques_key = array(
	"PRIMARY KEY"			=> "id_categorie, id_rubrique",
	"KEY id_rubrique"		=> "id_rubrique"
	);

$spip_echoppe_categories_articles = array(
	"id_categorie"			=> "bigint(21) NOT NULL",
	"id_article"			=> "bigint(21) NOT NULL"
	);

$spip_echoppe_categories_articles_key = array(
	"PRIMARY KEY"			=> "id_categorie,id_article",
	"KEY id_article"		=> "id_article"
	);

$spip_echoppe_produits_articles = array(
	"id_produit"			=> "bigint(21) NOT NULL",
	"id_article"			=> "bigint(21) NOT NULL"
	);

$spip_echoppe_produits_articles_key = array(
	"PRIMARY KEY"			=> "id_produit,id_article",
	"KEY id_article"			=> "id_article"
	);

$spip_echoppe_produits_rubriques = array(
	"id_produit"			=> "bigint(21) NOT NULL",
	"id_rubrique"			=> "bigint(21) NOT NULL"
	);

$spip_echoppe_produits_rubriques_key = array(
	"PRIMARY KEY"			=> "id_produit,id_rubrique",
	"KEY id_rubrique"			=> "id_rubrique"
	);

$spip_echoppe_produits_sites = array(
	"id_produit"			=> "bigint(21) NOT NULL",
	"id_site"				=> "bigint(21) NOT NULL"
	);

$spip_echoppe_produits_sites_key = array(
	"PRIMARY KEY"			=> "id_produit,id_site",
	"KEY id_site"			=> "id_site"
	);

$spip_echoppe_produits_documents = array(
	"id_produit"			=> "bigint(21) NOT NULL",
	"lang"				=> "VARCHAR(2) DEFAULT '' NOT NULL", // Dans le cas ou le doc sert d'illu de pochette de dvd par ex, mettre la bonne pochette avec le titre du film dans la langue visit�e
	"id_document"		=> "bigint(21) NOT NULL"
	);

$spip_echoppe_produits_documents_key = array(
	"PRIMARY KEY"		=> "id_produit,lang,id_document",
	"KEY lang"		=> "lang", // Dans le cas ou le doc sert d'illu de pochette de dvd par ex, mettre la bonne pochette avec le titre du film dans la langue visit�e
	"KEY id_document"	=> "id_document"
	);

//
// fin base des liens avec les objets spip
//



$spip_echoppe_options = array(
	"id_option"	=> "bigint(21) NOT NULL",
	"id_produit"	=> "bigint(21) DEFAULT '0' NOT NULL",//( si =0 et id_categorie != 0 c'est donc une option sur une categorie ) crowfoot +1
	"id_categorie"	=> "bigint(21) DEFAULT '0' NOT NULL"
	);

$spip_echoppe_options_key = array(
	"PRIMARY KEY"		=> "id_option,id_produit,id_categorie",
	"KEY id_option"		=> "id_option",
	"KEY id_produit"	=> "id_produit",//( si =0 et id_categorie != 0 c'est donc une option sur une categorie ) crowfoot +1
	"KEY id_categorie"	=> "id_categorie"
	);

$spip_echoppe_options_join = array(
	"id_option"	=> "id_option",
	"id_produit"	=> "id_produit",//( si =0 et id_categorie != 0 c'est donc une option sur une categorie ) crowfoot +1
	"id_categorie"	=> "id_categorie"
	);


$spip_echoppe_options_descriptifs = array( 
#	"id_traduction_options"		=> "bigint(21) NOT NULL",
	"id_option"			=> "bigint(21) DEFAULT '0' NOT NULL",
	"lang"				=> "VARCHAR(2) DEFAULT '' NOT NULL",
	"texte"				=> "text NOT NULL"
	);

$spip_echoppe_options_descriptifs_key = array( 
#	"PRIMARY KEY"		=> "id_traduction_options",
	"PRIMARY KEY"	=> "id_option,lang",
	"KEY id_option"	=> "id_option",
	"KEY lang"	=> "lang"
	);

$spip_echoppe_options_descriptifs_join = array( 
	"id_option"			=> "id_option",
	"lang"				=> "lang"
	);


$spip_echoppe_options_valeurs = array(
	"id_options_valeurs"		=> "bigint(21) NOT NULL",
	"id_option"			=> "bigint(21) NOT NULL",
	"valeur"			=> "text NOT NULL",
	"defaut"			=> "bool NOT NULL"
	);


$spip_echoppe_options_valeurs_key = array(
	"PRIMARY KEY"		=> "id_options_valeurs",
	"KEY id_option"		=> "id_option",
	"KEY defaut"		=> "defaut"
	);


$spip_echoppe_options_valeurs_join = array(
	"id_options_valeurs"		=> "id_options_valeurs",
	"id_option"			=> "id_option",
	"defaut"			=> "defaut"
	);


$spip_echoppe_options_valeurs_descriptifs = array(
#	"id_options_valeurs_descriptifs"	=> "bigint(21) NOT NULL",
	"id_options_valeurs"				=> "bigint(21) NOT NULL",
	"lang"						=> "VARCHAR(2) DEFAULT '' NOT NULL", //crowfoot : si langue ne vaut rien (ou 0), alors la valeur est accessible dans toutes les langues.( yoann : bonne id�e)
	"texte"						=> "text NOT NULL" //( pour une option couleur, on aura ici � rouge, vert � etc )
	);


$spip_echoppe_options_valeurs_descriptifs_key = array(
#	"id_options_valeurs_descriptifs"	=> "bigint(21) NOT NULL",
	"PRIMARY KEY"					=> "id_options_valeurs,lang",
	"KEY lang"					=> "lang", //crowfoot : si langue ne vaut rien (ou 0), alors la valeur est accessible dans toutes les langues.( yoann : bonne id�e)
	);


$spip_echoppe_options_valeurs_descriptifs_join = array(
#	"id_options_valeurs_descriptifs"	=> "bigint(21) NOT NULL",
	"id_options_valeurs"				=> "id_options_valeurs",
	"lang"						=> "lang", //crowfoot : si langue ne vaut rien (ou 0), alors la valeur est accessible dans toutes les langues.( yoann : bonne id�e)
	);


$spip_echoppe_prix = array(
	"id_prix"			=> "bigint(21) NOT NULL",
	"id_produit"			=> "bigint(21) NOT NULL",
	"configuration"			=> "longblob NOT NULL",
	"prix"				=> "float NOT NULL",
	"date_debut"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
	"date_fin"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL"
);

$spip_echoppe_prix_key = array(
	"PRIMARY KEY"			=> "id_prix",
	"KEY id_produit"		=> "id_produit",
	"KEY date_debut"		=> "date_debut",
	"KEY date_fin"			=> "date_fin"
);

$spip_echoppe_prix_join = array(
	"id_prix"			=> "id_prix",
	"id_produit"			=> "id_produit",
	"date_debut"			=> "date_debut",
	"date_fin"			=> "date_fin"
);


$spip_echoppe_client = array(
	"id_client"	=> "bigint(21) NOT NULL",
	"id_auteur"	=> "bigint(21) NOT NULL",
	"token_client"	=> "VARCHAR(255) NOT NULL"
	);


$spip_echoppe_client_key = array(
	"PRIMARY KEY"		=> "id_client",
	"KEY id_auteur"		=> "id_auteur",
	"KEY token_client"	=> "token_client"
	);


$spip_echoppe_client_join = array(
	"id_client"	=> "id_client",
	"id_auteur"	=> "id_auteur",
	"token_client"	=> "token_client"
	);

$spip_echoppe_panier = array(
	"id_panier"	=> "bigint(21) NOT NULL", //Un panier complet est constitue de plusieurs enregistrelent de cette table. Tous relies par token_panier
	"id_client"	=> "bigint(21) NOT NULL",
	"id_produit"	=> "bigint(21) NOT NULL",
	"quantite"	=> "bigint(21) NOT NULL",
	"configuration"	=> "longblob NOT NULL",
	"token_client"	=> "VARCHAR(255) NOT NULL",
	"token_panier"	=> "VARCHAR(255) NOT NULL"
	);


$spip_echoppe_panier_key = array(
	"PRIMARY KEY"		=> "id_panier",
	"KEY id_client"		=> "id_client",
	"KEY token_client"	=> "token_client",
	"KEY token_panier"	=> "token_panier"
	);


$spip_echoppe_panier_join = array(
	"id_panier"	=> "id_panier",
	"id_client"	=> "id_client",
	"token_client"	=> "token_client",
	"token_panier"	=> "token_panier"
	);



$tables_principales['spip_echoppe_categories'] = array(
	'field' => &$spip_echoppe_categories,
	'key' => &$spip_echoppe_categories_key,
	'join' => &$spip_echoppe_categories_join
	);

$tables_principales['spip_echoppe_categories_descriptions'] = array(
	'field' => &$spip_echoppe_categories_descriptions,
	'key' => &$spip_echoppe_categories_descriptions_key,
	'join' => &$spip_echoppe_categories_descriptions_join
	);

$tables_principales['spip_echoppe_produits'] = array(
	'field' => &$spip_echoppe_produits,
	'key' => &$spip_echoppe_produits_key,
	'join' => &$spip_echoppe_produits_join
	);

$tables_principales['spip_echoppe_produits_descriptions'] = array(
	'field' => &$spip_echoppe_produits_descriptions,
	'key' => &$spip_echoppe_produits_descriptions_key,
	'join' => &$spip_echoppe_produits_descriptions_join
	);

$tables_principales['spip_echoppe_categories_produits'] = array(
	'field' => &$spip_echoppe_categories_produits,
	'key' => &$spip_echoppe_categories_produits_key,
	'join' => &$spip_echoppe_categories_produits_join
	);

$tables_principales['spip_echoppe_gammes'] = array(
	'field' => &$spip_echoppe_gammes,
	'key' => &$spip_echoppe_gammes_key,
	'join' => &$spip_echoppe_gammes_join
	);

$tables_principales['spip_echoppe_gammes_produits'] = array(
	'field' => &$spip_echoppe_gammes_produits,
	'key' => &$spip_echoppe_gammes_produits_key,
	'join' => &$spip_echoppe_gammes_produits_join
	);

$tables_principales['spip_echoppe_categories_rubriques'] = array(
	'field' => &$spip_echoppe_categories_rubriques,
	'key' => &$spip_echoppe_categories_rubriques_key,
	'join' => &$spip_echoppe_categories_rubriques_join
	);
	
$tables_principales['spip_echoppe_categories_articles'] = array(
	'field' => &$spip_echoppe_categories_articles,
	'key' => &$spip_echoppe_categories_articles_key,
	'join' => &$spip_echoppe_categories_articles_join
	);

$tables_principales['spip_echoppe_produits_articles'] = array(
	'field' => &$spip_echoppe_produits_articles,
	'key' => &$spip_echoppe_produits_articles_key,
	'join' => &$spip_echoppe_produits_articles_join
	);

$tables_principales['spip_echoppe_produits_rubriques'] = array(
	'field' => &$spip_echoppe_produits_rubriques,
	'key' => &$spip_echoppe_produits_rubriques_key,
	'join' => &$spip_echoppe_produits_rubriques_join
	);

$tables_principales['spip_echoppe_produits_sites'] = array(
	'field' => &$spip_echoppe_produits_sites,
	'key' => &$spip_echoppe_produits_sites_key,
	'join' => &$spip_echoppe_produits_sites_join
	);

$tables_principales['spip_echoppe_produits_documents'] = array(
	'field' => &$spip_echoppe_produits_documents,
	'key' => &$spip_echoppe_produits_documents_key,
	'join' => &$spip_echoppe_produits_documents_join
	);

$tables_principales['spip_echoppe_options'] = array(
	'field' => &$spip_echoppe_options,
	'key' => &$spip_echoppe_options_key,
	'join' => &$spip_echoppe_options_join
	);

$tables_principales['spip_echoppe_options_descriptifs'] = array(
	'field' => &$spip_echoppe_options_descriptifs,
	'key' => &$spip_echoppe_options_descriptifs_key,
	'join' => &$spip_echoppe_options_descriptifs_join
	);

$tables_principales['spip_echoppe_options_valeurs'] = array(
	'field' => &$spip_echoppe_options_valeurs,
	'key' => &$spip_echoppe_options_valeurs_key,
	'join' => &$spip_echoppe_options_valeurs_join
	);

$tables_principales['spip_echoppe_options_valeurs_descriptifs'] = array(
	'field' => &$spip_echoppe_options_valeurs_descriptifs,
	'key' => &$spip_echoppe_options_valeurs_descriptifs_key,
	'join' => &$spip_echoppe_options_valeurs_descriptifs_join
	);

$tables_principales['spip_echoppe_prix'] = array(
	'field' => &$spip_echoppe_prix,
	'key' => &$spip_echoppe_prix_key,
	'join' => &$spip_echoppe_prix_join
	);

$tables_principales['spip_echoppe_client'] = array(
	'field' => &$spip_echoppe_client,
	'key' => &$spip_echoppe_client_key,
	'join' => &$spip_echoppe_client_join
	);

$tables_principales['spip_echoppe_panier'] = array(
	'field' => &$spip_echoppe_panier,
	'key' => &$spip_echoppe_panier_key,
	'join' => &$spip_echoppe_panier_join
	);

$tables_principales['spip_echoppe_depots'] = array(
	'field' => &$spip_echoppe_depots,
	'key' => &$spip_echoppe_depots_key,
	'join' => &$spip_echoppe_depots_join
	);
	
$tables_principales['spip_echoppe_stock_produits'] = array(
	'field' => &$spip_echoppe_stock_produits,
	'key' => &$spip_echoppe_stock_produits_key,
	'join' => &$spip_echoppe_stock_produits_join
	);

?>
