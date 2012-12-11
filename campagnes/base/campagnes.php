<?php
/**
 * Plugin Campagnes publicitaires
 * (c) 2012 Les Développements Durables
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function campagnes_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['encarts'] = 'encarts';
	$interfaces['table_des_tables']['campagnes'] = 'campagnes';
	$interfaces['table_des_tables']['annonceurs'] = 'annonceurs';
	$interfaces['table_des_tables']['campagnes_vues'] = 'campagnes_vues';
	$interfaces['table_des_tables']['campagnes_clics'] = 'campagnes_clics';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function campagnes_declarer_tables_objets_sql($tables) {

	$tables['spip_encarts'] = array(
		'type' => 'encart',
		'principale' => "oui",
		'field'=> array(
			"id_encart"          => "bigint(21) NOT NULL",
			"titre"              => "text not null default ''",
			"identifiant"        => "varchar(255) not null default ''",
			"largeur"            => "int(11) NOT NULL DEFAULT 0",
			"hauteur"            => "int(11) NOT NULL DEFAULT 0",
			"type"               => "varchar(50) NOT NULL DEFAULT 'image'",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_encart",
		),
		'titre' => "titre AS titre, '' AS lang",
		'champs_editables'  => array('titre', 'identifiant', 'largeur', 'hauteur', 'type'),
		'champs_versionnes' => array('titre', 'identifiant', 'largeur', 'hauteur', 'type'),
		'rechercher_champs' => array("titre" => 8, "identifiant" => 8),
		'tables_jointures'  => array(),
	);

	$tables['spip_campagnes'] = array(
		'type' => 'campagne',
		'principale' => "oui",
		'field'=> array(
			"id_campagne"         => "bigint(21) NOT NULL",
			"id_encart"          => "bigint(21) NOT NULL DEFAULT 0",
			"titre"              => "text NOT NULL DEFAULT ''",
			"descriptif"         => "text NOT NULL DEFAULT ''",
			"url"                => "text NOT NULL DEFAULT ''",
			"id_annonceur"       => "bigint(21) NOT NULL DEFAULT 0",
			"date_debut"         => "date NOT NULL DEFAULT '0000-00-00'",
			"date_fin"           => "date NOT NULL DEFAULT '0000-00-00'",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"clics_max"          => "int not null default 0",
			"clics_reste"        => "int not null default 0",
			"vues_max"           => "int not null default 0",
			"vues_reste"         => "int not null default 0",
			"contextes"          => "text not null default ''",
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_campagne",
			"KEY statut"         => "statut", 
		),
		'titre' => "titre AS titre, '' AS lang",
		'date' => "date",
		'champs_editables'  => array('id_encart', 'titre', 'descriptif', 'url', 'id_annonceur', 'date_debut', 'date_fin', 'contextes'),
		'champs_versionnes' => array('id_encart', 'titre', 'descriptif', 'url', 'id_annonceur', 'date_debut', 'date_fin', 'contextes'),
		'rechercher_champs' => array("titre" => 8, "descriptif" => 4),
		'tables_jointures'  => array(),
		'statut_textes_instituer' => array(
			'prepa'    => 'campagne:statut_prepa_texte',
			'publie'   => 'texte_statut_publie',
			'obsolete' => 'campagne:statut_obsolete_texte',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut_images' => array(
			'prepa' => 'puce-preparer-8.png',
			'publie' => 'puce-publier-8.png',
			'obsolete' => 'puce-refuser-8.png',
			'poubelle' => 'puce-supprimer-8.png',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'publie',
				'previsu'   => 'publie,prepa,obsolete',
				'post_date' => 'date', 
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'campagne:texte_changer_statut_campagne', 
	);

	$tables['spip_annonceurs'] = array(
		'type' => 'annonceur',
		'principale' => "oui",
		'field'=> array(
			"id_annonceur"       => "bigint(21) NOT NULL",
			"id_auteur"          => "bigint(21) NOT NULL DEFAULT 0",
			"nom"                => "text NOT NULL DEFAULT ''",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_annonceur",
		),
		'titre' => "nom AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('id_auteur', 'nom'),
		'champs_versionnes' => array('id_auteur', 'nom'),
		'rechercher_champs' => array("nom" => 8),
		'tables_jointures'  => array(),
	);

	return $tables;
}

/*
 * Déclaration des tables de statistiques
 */
function campagnes_declarer_tables_auxiliaires($tables_auxiliaires){
	// Table de comptage des affichages
	$campagnes_vues = array(
		'id_campagne' => 'bigint(21) not null default 0',
		'id_encart' => 'bigint(21) not null default 0',
		'date' => 'date not null default "0000-00-00"',
		'id_auteur' => 'bigint(21) not null default 0',
		'cookie' => 'varchar(255) not null default ""',
		'ip' => 'varchar(255) not null default ""',
		'page' => 'text not null default""',
	);
	
	$campagnes_vues_cles = array(
		'PRIMARY KEY' => 'id_campagne, id_encart, date, cookie',
		'KEY id_campagne' => 'id_campagne',
		'KEY id_encart' => 'id_encart',
		'KEY id_auteur' => 'id_auteur',
	);
	
	$tables_auxiliaires['spip_campagnes_vues'] = array(
		'field' => &$campagnes_vues,
		'key' => &$campagnes_vues_cles,
		'join' => array('id_campagne' => 'id_campagne', 'id_encart' => 'id_encart')
	);
	
	// Table de comptage des clics
	$campagnes_clics = array(
		'id_campagne' => 'bigint(21) not null default 0',
		'id_encart' => 'bigint(21) not null default 0',
		'date' => 'date not null default "0000-00-00"',
		'id_auteur' => 'bigint(21) not null default 0',
		'cookie' => 'varchar(255) not null default ""',
		'ip' => 'varchar(255) not null default ""',
		'page' => 'text not null default""',
	);
	
	$campagnes_clics_cles = array(
		'PRIMARY KEY' => 'id_campagne, id_encart, date, cookie',
		'KEY id_campagne' => 'id_campagne',
		'KEY id_encart' => 'id_encart',
		'KEY id_auteur' => 'id_auteur',
	);
	
	$tables_auxiliaires['spip_campagnes_clics'] = array(
		'field' => &$campagnes_clics,
		'key' => &$campagnes_clics_cles,
		'join' => array('id_campagne' => 'id_campagne', 'id_encart' => 'id_encart')
	);
	
	return $tables_auxiliaires;
}

?>
