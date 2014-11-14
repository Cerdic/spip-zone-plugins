<?php
/**
 * Plugin Partageur
 * 
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function partageur_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['partageurs'] = 'partageurs';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function partageur_declarer_tables_objets_sql($tables) {

	$tables['spip_partageurs'] = array(
		'type' => 'partageur',
		'principale' => "oui",
		'field'=> array(
			"id_partageur"       => "bigint(21) NOT NULL",
			"titre"              => "text NOT NULL DEFAULT ''",
			"url_site"           => "varchar(255) NOT NULL DEFAULT ''",
			"cle"                => "varchar(255) NOT NULL DEFAULT ''",
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_partageur",
			"KEY statut"         => "statut", 
		),
		'titre' => "titre AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('titre', 'url_site', 'cle'),
		'champs_versionnes' => array('url_site', 'cle'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),
		'statut_textes_instituer' => array(
			'prepa'    => 'texte_statut_en_cours_redaction',
			'prop'     => 'texte_statut_propose_evaluation',
			'publie'   => 'texte_statut_publie',
			'refuse'   => 'texte_statut_refuse',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'publie',
				'previsu'   => 'publie,prop,prepa',
				'post_date' => 'date', 
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'partageur:texte_changer_statut_partageur', 
		

	);

	return $tables;
}



?>