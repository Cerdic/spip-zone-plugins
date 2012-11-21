<?php
/**
 * Plugin mailsuscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function mailsuscribers_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['mailsuscribers'] = 'mailsuscribers';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function mailsuscribers_declarer_tables_objets_sql($tables) {

	$tables['spip_mailsuscribers'] = array(
		'type' => 'mailsuscriber',
		'principale' => "oui",
		'field'=> array(
			"id_mailsuscriber"   => "bigint(21) NOT NULL",
			"email"              => "text NOT NULL DEFAULT ''",
			"nom"                => "text NOT NULL DEFAULT ''",
			"listes"             => "text NOT NULL DEFAULT ''",
			"optin"              => "text NOT NULL DEFAULT ''",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"statut"             => "varchar(20)  DEFAULT 'prepa' NOT NULL",
			"jeton"             => "char(25)  DEFAULT '' NOT NULL",
			"lang"               => "VARCHAR(10) NOT NULL DEFAULT ''",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_mailsuscriber",
			"KEY lang"           => "lang", 
			"KEY statut"         => "statut", 
		),
		'titre' => "email AS titre, lang AS lang",
		'date' => "date",
		'champs_editables'  => array('email', 'nom', 'listes', 'lang'),
		'champs_versionnes' => array('email', 'nom', 'listes', 'lang'),
		'rechercher_champs' => array("email" => 1, "nom" => 1),
		'tables_jointures'  => array(),
		'statut_textes_instituer' => array(
			'prepa'    => 'mailsuscriber:texte_statut_pas_encore_inscrit',
			'prop'    => 'mailsuscriber:texte_statut_en_attente_confirmation',
			'valide'   => 'mailsuscriber:texte_statut_valide',
			'refuse'   => 'mailsuscriber:texte_statut_refuse',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut_images' => array(
			'prepa'=>'puce-preparer-8.png',
			'prop'=>'puce-proposer-8.png',
			'valide'=>'puce-publier-8.png',
			'refuse'=>'puce-refuser-8.png',
			'poubelle'=>'puce-supprimer-8.png',
		),
		'statut_titres' => array(
			'prepa'=>'mailsuscriber:info_statut_prepa',
			'prop'=>'mailsuscriber:info_statut_prop',
			'valide'=>'mailsuscriber:info_statut_valide',
			'refuse'=>'mailsuscriber:info_statut_refuse',
			'poubelle'=>'mailsuscriber:info_statut_poubelle',
		),

		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'valide',
				'previsu'   => 'valide,prop,prepa',
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'mailsuscriber:texte_changer_statut_mailsuscriber', 

	);

	return $tables;
}



?>