<?php
/* Déclaration des paramétres de configuration */

global $corbeille_param;
$corbeille_param = array (
	"signatures"=> 		array(	"statut" => "poubelle", 
								"titre" => "nom_email", 
								"table" => "spip_signatures", 
								"id" => "id_signature",
								"temps" => "date_time",
								"page_voir" => array($page4,'id_document'),
								"libelle" => _L("Toutes les p&eacute;titions dans la corbeille :")
								),
	"breves"=>	 		array(	"statut" => "refuse", 
								"titre" => "titre",
								"table" => "spip_breves", 
								"id" => "id_breve",
								"temps" => "date_heure",
								"page_voir" => array("breves_voir",'id_breve'),
								"libelle" => _L("Toutes les br&egrave;ves dans la corbeille :")
								),
	"articles"=>	 	array(	"statut" => "poubelle",
								"titre" => "titre",
								"table" => "spip_articles",
								"id" => "id_article",
								"temps" => "date",
								"page_voir" => array("articles",'id_article'),
								"libelle" => _L("Tous les articles dans la corbeille :")
								),
	"forums_publics"=>	array(	"statut" => "off",
								"titre" => "titre",
								"table" => "spip_forum",
								"id" => "id_forum",
								"temps" => "date_heure",
								"libelle" => _L("Tous les messages du forum dans la corbeille :")
								),
	"forums_prives"=>	array(	"statut" => "privoff",
								"titre" => "titre",
								"table" => "spip_forum",
								"id" => "id_forum",
								"temps" => "date_heure",
								"libelle" => _L("Tous les messages du forum dans la corbeille :")
								),
	"auteurs" =>		array(	"statut" => "5poubelle",
								"titre" => "nom",
								"table"=>"spip_auteurs",
								"id"=>"id_auteur",
								"temps" => "maj",
								"page_voir" => array("auteurs_edit",'id_auteur'),
								"libelle" => _L("Tous les auteurs dans la corbeille :")
								)					
	);

//print_r($corbeille_param["articles"]["statut"]);

?>
