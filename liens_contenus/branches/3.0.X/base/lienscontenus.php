<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Déclaration des tables interfaces
 *
 * @param Array $interface
 */
function lienscontenus_declarer_tables_interfaces($interface){
	// Pas de tables d'interface ?
	return $interface;
}

/**
 * Déclaration des tables / objets SQL
 *
 * @param Array $tables
 */
function lienscontenus_declarer_tables_objets_sql($tables){
	$tables['spip_liens_contenus'] = array(
		/*
		 * Base de données
		 */
		 
		/* Declarations principales */
		'table_objet' => 'liens',
		'type' => 'lien',

		/* La table */
		'field'=> array(
				"type_objet_contenant"  => "varchar(10)",
				"id_objet_contenant"    => "int UNSIGNED NOT NULL",
				"type_objet_contenu"    => "varchar(10)",
				"id_objet_contenu"      => "varchar(255)",
		),
		
		/* Les clés */
		'key' => array(
				"PRIMARY KEY"   => "type_objet_contenant, id_objet_contenant, type_objet_contenu, id_objet_contenu",
				"KEY contenant" => "type_objet_contenant, id_objet_contenant",
				"KEY contenu" => "type_objet_contenu, id_objet_contenu"
		),
		
		'principale' => "oui",
		
		/* 
		 * Le titre, la date et la gestion du statut 
		 */
		 'titre' => "titre AS titre, '' AS lang",
		 
		 /*
		  * L’édition, l’affichage et la recherche
		  */
		  
		'page' => false,
		
		/* champs_editables */
		'champs_editables' => array(),
		
		/* Les chaines de langue */
	);
	
	$tables['spip_liens_contenus_todo'] = array(
		/*
		 * Base de données
		 */
		 
		/* Declarations principales */
		'table_objet' => 'liens_todo',
		'type' => 'lien_todo',

		/* La table */
		'field'=> array(
				"type_objet_contenant"  => "varchar(10)",
				"id_objet_contenant"    => "int UNSIGNED NOT NULL",
				"date_added"            => "int(11) NOT NULL",
		),
		
		/* Les clés */
		'key' => array(
				"PRIMARY KEY"   => "type_objet_contenant, id_objet_contenant"
		),
		
		'principale' => "oui",
		
		/* 
		 * Le titre, la date et la gestion du statut 
		 */
		 'titre' => "titre AS titre, '' AS lang",
		 
		 /*
		  * L’édition, l’affichage et la recherche
		  */
		  
		'page' => false,
		
		/* champs_editables */
		'champs_editables' => array(),
		
		/* Les chaines de langue */
	);
	

	return $tables;
}

/**
 * Déclaration des tables auxiliaires
 *
 * @param Array $tables_auxiliaires
 */
function lienscontenus_declarer_tables_auxiliaires($tables_auxiliaires){
	// Pas de tables d'interface ?
	return $tables_auxiliaires;
}

