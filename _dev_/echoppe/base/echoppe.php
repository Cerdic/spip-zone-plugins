<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
	
include_spip('base/serial');

global $tables_principales;
global $tables_auxiliaires;

$spip_echoppe_categories = array(
	"id_categorie"	=> "bigint(21) NOT NULL",
	"id_parent"	=> "bigint(21) NOT NULL"
	);
	
$spip_echoppe_categories_key = array(
	"PRIMARY KEY"        => "id_categorie",
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
	"statut"					=> "varchar(10) DEFAULT '0' NOT NULL"	// ( je le mets ici, comme ça, on peut avoir moins de catégories dans les langues étrangères)
	);

$spip_echoppe_categories_descriptions_key = array(
	"PRIMARY KEY"		=> "id_categorie_description",
	"KEY id_categorie"    => "id_categorie",
	"KEY lang"		=> "lang",
	);

$spip_echoppe_produits = array(
	"id_produit"				=> "bigint(21) NOT NULL",
	"date_mise_en_ligne"		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
	"date_retrait_mise_en_ligne"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
	"poids"					=> "float DEFAULT '0' NOT NULL", 	// (crowfoot : probablement pas internationalisé pour le calcul des frais de port)
	"hauteur"					=> "float DEFAULT '0' NOT NULL", 	// (crowfoot : serait important pour le calcul des frais de livraison ...)
	"largeur"					=> "float DEFAULT '0' NOT NULL",
	"longueur"				=> "float DEFAULT '0' NOT NULL",
	"ref_produit"				=> "text NOT NULL",
	"maj"					=> "TIMESTAMP",
	"statut"					=> "varchar(10) DEFAULT '0' NOT NULL"
	);

$spip_echoppe_produits_descriptions = array(
	"id_descriptif_produit"	=> "bigint(21) NOT NULL",
	"id_produit"			=> "bigint(21) NOT NULL",
	"lang"				=> "VARCHAR(2) DEFAULT '' NOT NULL",
	"titre"				=> "text NOT NULL", 
	"descriptif"			=> "text NOT NULL",
	"texte"				=> "longblob NOT NULL",
	"ps"					=> "text NOT NULL",
	"prix_base_htva"		=> "float DEFAULT '0' NOT NULL", 	//(permettrait de donner un prix de base et pas faire 50000 jointures pour retrouver le prix dans une liste de produits....)
	"tva"					=> "float DEFAULT '0' NOT NULL",	// (doit à mon avis être internationalisé... non ? )
	"quantite_mini"		=> "int NOT NULL",
	"maj"				=> "TIMESTAMP",
	"colisage"			=> "VARCHAR(10) DEFAULT '' NOT NULL"//(ça pourrait etre une donnée d'info sur la taille du colis ( ou lettre, ou palettes ... etc )
	);

$spip_echoppe_categories_produits = array(
	"id_categorie"			=> "bigint(21) NOT NULL",
	"id_produit"			=> "bigint(21) NOT NULL"
	);

// Un produits peut faire partie d'une catégorie : écran smasung, mais aussi d'une gamme : "SyncMaster" ou "Ordinateur complet" 
// A voir si ca fait pas double emplois avec les mots clés
$spip_echoppe_gammes = array(
	"id_gamme"			=> "bigint(21) NOT NULL",
	"titre"				=> "text NOT NULL", // On se pete pas la tete, on utilise les multi
	"descriptif"			=> "text NOT NULL"
	);

$spip_echoppe_gammes_produits = array(
	"id_gamme"			=> "bigint(21) NOT NULL",
	"id_produit"			=> "bigint(21) NOT NULL"
	);




//
// Base des liens avec les objets spip
//
$spip_echoppe_categories_rubriques = array(
	"id_categorie"			=> "bigint(21) NOT NULL",
	"id_rubrique"			=> "bigint(21) NOT NULL"
	);

$spip_echoppe_categories_articles = array(
	"id_categorie"			=> "bigint(21) NOT NULL",
	"id_article"			=> "bigint(21) NOT NULL"
	);

$spip_echoppe_produits_articles = array(
	"id_produit"			=> "bigint(21) NOT NULL",
	"id_article"			=> "bigint(21) NOT NULL"
	);

$spip_echoppe_produits_rubriques = array(
	"id_produit"			=> "bigint(21) NOT NULL",
	"id_rubrique"			=> "bigint(21) NOT NULL"
	);

$spip_echoppe_produits_sites = array(
	"id_produit"			=> "bigint(21) NOT NULL",
	"id_site"				=> "bigint(21) NOT NULL"
	);

$spip_echoppe_produits_documents = array(
	"id_produit"			=> "bigint(21) NOT NULL",
	"lang"				=> "VARCHAR(2) DEFAULT '' NOT NULL", // Dans le cas ou le doc sert d'illu de pochette de dvd par ex, mettre la bonne pochette avec le titre du film dans la langue visitée
	"id_documents"		=> "bigint(21) NOT NULL"
	);
//
// fin base des liens avec les objets spip
//



$spip_echoppe_options = array(
	"id_option"			=> "bigint(21) NOT NULL",
	"id_produit"			=> "bigint(21) DEFAULT '0' NOT NULL",//( si =0 et id_categorie != 0 c'est donc une option sur une categorie ) crowfoot +1
	"id_categorie"			=> "bigint(21) DEFAULT '0' NOT NULL"
	);

$spip_echoppe_options_descriptifs = array( 
	"id_traduction_options"	=> "bigint(21) NOT NULL",
	"id_option"			=> "bigint(21) DEFAULT '0' NOT NULL",
	"lang"				=> "VARCHAR(2) DEFAULT '' NOT NULL",
	"texte"				=> "text NOT NULL"
	);

$spip_echoppe_options_valeurs = array(
	"id_options_valeurs"		=> "bigint(21) NOT NULL",
	"id_option"			=> "bigint(21) NOT NULL",
	"valeur"				=> "text NOT NULL",
	"defaut"				=> "bool NOT NULL"
	);
	
$spip_echoppe_options_valeurs_descriptifs = array(
	"id_options_valeurs_descriptifs"	=> "bigint(21) NOT NULL",
	"id_options_valeurs"				=> "bigint(21) NOT NULL",
	"lang"						=> "VARCHAR(2) DEFAULT '' NOT NULL", //crowfoot : si langue ne vaut rien (ou 0), alors la valeur est accessible dans toutes les langues.( yoann : bonne idée)
	"texte"						=> "text NOT NULL" //( pour une option couleur, on aura ici « rouge, vert » etc )
	);

$spip_echoppe_prix = array(
	"id_prix"				=> "bigint(21) NOT NULL",
	"id_produit"			=> "bigint(21) NOT NULL",
	"configuration"			=> "longblob NOT NULL",
	"prix"				=> "float NOT NULL",
	"date_debut"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
	"date_fin "			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL"
	
);

$tables_principales['spip_articles'] =
	array('field' => &$spip_articles, 'key' => &$spip_articles_key);

?>