<?php
/**
 *\file inc_param.php
 * Paramétre de configuration de la corbeille
 * "nom de l'objet spip" => array ("statut" => nom du statut dans la base de données (bdd),
 * 									"titre" => nom du champ retourné dans le listing,
 * 									"table" => nom de la table spip dans la bdd,
 * 									"id" => clef primaire dans la table,
 * 									"temps" => aucune idée à quoi ça peut servir,
 * 									"page_voir" => parametres pour voir le détail d'un objet
 * 									"libelle" => texte long dans la partie droite de l'affichage,
 * 									"libelle_court" => texte court dans le menu gauche,
 * 									"tablelie"  => tableau des tables spip à vider en meme temps    )  
 *
 */  

/* Déclaration des paramétres de configuration */
global $corbeille_param;
$corbeille_param = array (
	"signatures"=> 		array(	"statut" => "poubelle", 
								"titre" => "nom_email", 
								"table" => "spip_signatures", 
								"id" => "id_signature",
								"temps" => "date_time",
								"page_voir" => array("signatures",'id_document'),
								"libelle" => _L("Toutes les p&eacute;titions dans la corbeille :"),
								"libelle_court" => strtolower(_T('lien_petitions')),
								),
	"breves"=>	 		array(	"statut" => "refuse", 
								"titre" => "titre",
								"table" => "spip_breves", 
								"id" => "id_breve",
								"temps" => "date_heure",
								"page_voir" => array("breves_voir",'id_breve'),
								"libelle" => _L("Toutes les br&egrave;ves dans la corbeille :"),
								"libelle_court" => _T('icone_breves')
								),
	"articles"=>	 	array(	"statut" => "poubelle",
								"titre" => "titre",
								"table" => "spip_articles",
								"tableliee"=> array("spip_auteurs_articles","spip_documents_articles","spip_mots_articles","spip_signatures","spip_versions","spip_versions_fragments","spip_forum"),
								"id" => "id_article",
								"temps" => "date",
								"page_voir" => array("articles",'id_article'),
								"libelle" => _L("Tous les articles dans la corbeille :"),
								"libelle_court" => _T('icone_articles')
								),
	"forums_publics"=>	array(	"statut" => "off",
								"titre" => "titre",
								"table" => "spip_forum",
								"id" => "id_forum",
								"temps" => "date_heure",
								"libelle" => _L("Tous les messages du forum dans la corbeille :"),
								"libelle_court" => _T('titre_forum')
								),
	"forums_prives"=>	array(	"statut" => "privoff",
								"titre" => "titre",
								"table" => "spip_forum",
								"id" => "id_forum",
								"temps" => "date_heure",
								"libelle" => _L("Tous les messages du forum dans la corbeille :"),
								"libelle_court" => _T('icone_forum_administrateur')
								),
	"auteurs" =>		array(	"statut" => "5poubelle",
								"titre" => "nom",
								"table"=>"spip_auteurs",
								"id"=>"id_auteur",
								"temps" => "maj",
								"page_voir" => array("auteurs_edit",'id_auteur'),
								"libelle" => _L("Tous les auteurs dans la corbeille :"),
								"libelle_court" => _T('icone_auteurs')
								),					
	"syndic" =>			array(	"statut" => "refuse",
								"titre" => "nom_site",
								"table"=>"spip_syndic",
								"tableliee"=> array("spip_syndic_articles","spip_mots_syndic"),
								"id"=>"id_syndic",
								"temps" => "maj",
								"page_voir" => array("sites",'id_syndic'),
								"libelle" => _L("Tous les syndications dans la corbeille :"),
								"libelle_court" => _T('titre_syndication')
								)	
	);

?>
