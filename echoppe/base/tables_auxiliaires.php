<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function echoppe_tables_auxiliaires($tables_auxiliaires){
		
	$spip_categories_produits = array(
		"id_categorie"			=> "bigint(21) NOT NULL",
		"id_produit"			=> "bigint(21) NOT NULL"
		);

	$spip_categories_produits_key = array(
		"PRIMARY KEY"			=> "id_categorie, id_produit",
		"KEY id_produit"		=> "id_produit"
		);

	$spip_categories_produits_join = array(
		"id_categorie"			=> "id_categorie",
		"id_produit"			=> "id_produit"
		);
	
	$tables_auxiliaires['spip_echoppe_categories_produits'] = array(
		'field' => &$spip_categories_produits,
		'key' => &$spip_categories_produits_key,
		'join' => &$spip_categories_produits_join
	);
	
	$spip_gammes_produits = array(
		"id_gamme"			=> "bigint(21) NOT NULL",
		"id_produit"			=> "bigint(21) NOT NULL"
		);

	$spip_gammes_produits_key = array(
		"PRIMARY KEY"			=> "id_gamme",
		"KEY id_produit"		=> "id_produit"
		);

	$spip_gammes_produits_join = array(
		"id_gamme"			=> "id_gamme",
		"id_produit"			=> "id_produit"
		);
	
	$tables_auxiliaires['spip_echoppe_gammes_produits'] = array(
		'field' => &$spip_gammes_produits,
		'key' => &$spip_gammes_produits_key,
		'join' => &$spip_gammes_produits_join
	);
	
	$spip_categories_rubriques = array(
		"id_categorie"			=> "bigint(21) NOT NULL",
		"id_rubrique"			=> "bigint(21) NOT NULL"
		);

	$spip_categories_rubriques_key = array(
		"PRIMARY KEY"			=> "id_categorie, id_rubrique",
		"KEY id_rubrique"		=> "id_rubrique"
		);

	$spip_categories_rubriques_join = array(
		"id_categorie"			=> "id_categorie",
		"id_rubrique"		=> "id_rubrique"
		);

	$tables_auxiliaires['spip_echoppe_categories_rubriques'] = array(
		'field' => &$spip_categories_rubriques,
		'key' => &$spip_categories_rubriques_key,
		'join' => &$spip_categories_rubriques_join
	);
	
	$spip_categories_articles = array(
		"id_categorie"			=> "bigint(21) NOT NULL",
		"id_article"			=> "bigint(21) NOT NULL"
		);

	$spip_categories_articles_key = array(
		"PRIMARY KEY"			=> "id_categorie,id_article",
		"KEY id_article"		=> "id_article"
		);

	$spip_categories_articles_join = array(
		"id_categorie"			=> "id_categorie",
		"id_article"		=> "id_article"
		);

	$tables_auxiliaires['spip_echoppe_categories_articles'] = array(
		'field' => &$spip_categories_articles,
		'key' => &$spip_categories_articles_key,
		'join' => &$spip_categories_articles_join
	);
	
	$spip_produits_articles = array(
		"id_produit"			=> "bigint(21) NOT NULL",
		"id_article"			=> "bigint(21) NOT NULL"
		);

	$spip_produits_articles_key = array(
		"PRIMARY KEY"			=> "id_produit,id_article",
		"KEY id_article"			=> "id_article"
		);

	$spip_produits_articles_join = array(
		"id_produit"			=> "id_produit",
		"id_article"			=> "id_article"
		);

	$tables_auxiliaires['spip_echoppe_produits_articles'] = array(
		'field' => &$spip_produits_articles,
		'key' => &$spip_produits_articles_key,
		'join' => &$spip_produits_articles_join
	);
	
	$spip_produits_rubriques = array(
		"id_produit"			=> "bigint(21) NOT NULL",
		"id_rubrique"			=> "bigint(21) NOT NULL"
		);

	$spip_produits_rubriques_key = array(
		"PRIMARY KEY"			=> "id_produit,id_rubrique",
		"KEY id_rubrique"			=> "id_rubrique"
		);

	$spip_produits_rubriques_join= array(
		"id_produit"			=> "id_produit",
		"id_rubrique"			=> "id_rubrique"
		);

	$tables_auxiliaires['spip_echoppe_produits_rubriques'] = array(
		'field' => &$spip_produits_rubriques,
		'key' => &$spip_produits_rubriques_key,
		'join' => &$spip_produits_rubriques_join
	);
	
	$spip_produits_sites = array(
		"id_produit"			=> "bigint(21) NOT NULL",
		"id_site"				=> "bigint(21) NOT NULL"
		);

	$spip_produits_sites_key = array(
		"PRIMARY KEY"			=> "id_produit,id_site",
		"KEY id_site"			=> "id_site"
		);
	
	$spip_produits_sites_join = array(
		"id_produit"			=> "id_produit",
		"id_site"			=> "id_site"
		);

	$tables_auxiliaires['spip_echoppe_produits_sites'] = array(
		'field' => &$spip_produits_sites,
		'key' => &$spip_produits_sites_key,
		'join' => &$spip_produits_sites_join
	);
	
	$spip_produits_documents = array(
		"id_produit"			=> "bigint(21) NOT NULL",
		"id_document"		=> "bigint(21) NOT NULL"
		);

	$spip_produits_documents_key = array(
		"PRIMARY KEY"		=> "id_produit,id_document",
		"KEY id_document"	=> "id_document"
		);	
		

	$spip_produits_documents_join = array(
		"id_produit"		=> "id_produit",
		"id_document"	=> "id_document"
		);	
	
	$tables_auxiliaires['spip_echoppe_produits_documents'] = array(
		'field' => &$spip_produits_documents,
		'key' => &$spip_produits_documents_key,
		'join' => &$spip_produits_documents_join
	);
	
	return $tables_auxiliaires;
}

?>
