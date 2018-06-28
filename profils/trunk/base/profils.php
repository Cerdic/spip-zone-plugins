<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Profils
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Profils\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function profils_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['profils'] = 'profils';

	return $interfaces;
}

/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function profils_declarer_tables_objets_sql($tables) {
	$tables['spip_profils'] = array(
		'type' => 'profil',
		'principale' => 'oui',
		'field'=> array(
			'id_profil'          => 'bigint(21) NOT NULL',
			'titre'              => 'text NOT NULL DEFAULT ""',
			'identifiant'        => 'varchar(255) NOT NULL DEFAULT ""',
			'config'             => 'text not null default ""',
			'statut'             => 'varchar(20)  DEFAULT "0" NOT NULL',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_profil',
			'KEY statut'         => 'statut',
		),
		'titre' => 'titre AS titre, "" AS lang',
		 #'date' => '',
		'champs_editables'  => array('titre', 'identifiant', 'config'),
		'champs_versionnes' => array('titre', 'identifiant', 'config'),
		'rechercher_champs' => array("titre" => 10, "identifiant" => 10),
		'tables_jointures'  => array(),
		'statut_textes_instituer' => array(
			'prepa'    => 'texte_statut_en_cours_redaction',
			'publie'   => 'texte_statut_publie',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'publie',
				'previsu'   => 'publie,prepa',
				'post_date' => 'date',
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'profil:texte_changer_statut_profil',
	);
	
	// Ajouter un champ de profil aux comptes utilisateurs
	$tables['spip_auteurs']['field']['id_profil'] = 'bigint(21) not null default 0';
	$tables['spip_auteurs']['champs_editables'][] = 'id_profil';
	$tables['spip_auteurs']['champs_versionnes'][] = 'id_profil';

	return $tables;
}
