<?php
/**
 * Plugin À vos souhaits
 * (c) 2012 RastaPopoulos
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function souhaits_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['souhaits'] = 'souhaits';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function souhaits_declarer_tables_objets_sql($tables) {

	$tables['spip_souhaits'] = array(
		'type' => 'souhait',
		'principale' => "oui",
		'field'=> array(
			"id_souhait"         => "bigint(21) NOT NULL",
			"id_rubrique"        => "bigint(21) NOT NULL DEFAULT 0", 
			"id_secteur"         => "bigint(21) NOT NULL DEFAULT 0", 
			"titre"              => "text not null",
			"descriptif"         => "text not null default ''",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"prix"               => "float not null default 0",
			"nom_site"           => "varchar(255) not null default ''",
			"url_site"           => "varchar(255) not null default ''",
			"propositions"       => "text not null default ''",
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_souhait",
			"KEY id_rubrique"    => "id_rubrique", 
			"KEY id_secteur"     => "id_secteur", 
			"KEY statut"         => "statut", 
		),
		'titre' => "titre AS titre, '' AS lang",
		'date' => "date",
		'champs_editables'  => array('titre', 'descriptif', 'prix', 'nom_site', 'url_site'),
		'champs_versionnes' => array('titre', 'descriptif', 'date', 'prix', 'nom_site', 'url_site', 'statut'),
		'rechercher_champs' => array("titre" => 8, "descriptif" => 5),
		'tables_jointures'  => array(),
		'statut_textes_instituer' => array(
			'prepa' => 'texte_statut_en_cours_redaction',
			'libre' => 'souhait:statut_libre_label',
			'cagnotte' => 'souhait:statut_cagnotte_label',
			'propose' => 'souhait:statut_propose_label',
			'achete' => 'souhait:statut_achete_label',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut_images' => array(
			'prepa' => 'puce-preparer-8.png',
			'libre' => 'puce-publier-8.png',
			'cagnotte' => 'puce-proposer-8.png',
			'propose' => 'puce-refuser-8.png',
			'achete' => 'puce-refuser-8.png',
			'poubelle' => 'puce-supprimer-8.png',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'libre,propose,cagnotte,achete',
				'previsu'   => 'libre,propose,cagnotte,achete,prepa',
				'post_date' => 'date', 
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'souhait:texte_changer_statut_souhait', 
		

	);

	return $tables;
}



?>
