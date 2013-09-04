<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function newsletters_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['newsletters'] = 'newsletters';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function newsletters_declarer_tables_objets_sql($tables) {

	$tables['spip_newsletters'] = array(
		'type' => 'newsletter',
		'principale' => "oui",
		'field'=> array(
			"id_newsletter"      => "bigint(21) NOT NULL",
			"titre"              => "text NOT NULL DEFAULT ''",
			"chapo"              => "mediumtext NOT NULL DEFAULT ''",
			"texte"              => "longtext NOT NULL DEFAULT ''",
			"date_redac"         => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"patron"             => "text NOT NULL DEFAULT ''",
			// pour indiquer quand la NL est figee est ne doit plus etre re-generee
			// baked = 0 : mode d'edition des champs&articles et generee a chaque modeif&preview
			// baked = 1 : mode d'edition avance du html et texte, plus de generation auto
			"baked"              => "tinyint NOT NULL DEFAULT 0",
			/* Version email html et email texte */
			"html_email"         => "longtext NOT NULL DEFAULT ''",
			"texte_email"        => "longtext NOT NULL DEFAULT ''",
			/* Version page html pour afficher dans le navigateur (version en ligne) */
			"html_page"          => "longtext NOT NULL DEFAULT ''",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			/* pour la programmation */
			"recurrence"         => "text NOT NULL DEFAULT ''",
			"email_test"         => "text NOT NULL DEFAULT ''",
			"liste"              => "text NOT NULL DEFAULT ''",

			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL",
			"lang"               => "VARCHAR(10) NOT NULL DEFAULT ''",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_newsletter",
			"KEY lang"           => "lang", 
			"KEY statut"         => "statut", 
		),
		'titre' => "titre AS titre, lang AS lang",
		'date' => "date",
		'champs_editables'  => array('titre','chapo','texte','date_redac','patron','baked','html_email','texte_email','html_page','recurrence','email_test','liste'),
		'champs_versionnes' => array(),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(
			'mots_liens',
			'newsletters_liens'
		),
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
		'texte_changer_statut' => 'newsletter:texte_changer_statut_newsletter', 
		

	);

	$tables['spip_articles']['tables_jointures'][]= 'newsletters_liens';
	$tables['spip_rubriques']['tables_jointures'][]= 'newsletters_liens';
	$tables['spip_rubriques']['tables_jointures'][]= 'newsletters_liens';

	return $tables;
}


/**
 * Déclaration des tables secondaires (liaisons)
 */
function newsletters_declarer_tables_auxiliaires($tables) {

	$tables['spip_newsletters_liens'] = array(
		'field' => array(
			"id_newsletter"      => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_newsletter,id_objet,objet",
			"KEY id_newsletter"  => "id_newsletter"
		)
	);

	return $tables;
}


?>