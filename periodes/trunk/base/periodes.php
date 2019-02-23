<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Périodes
 * @copyright  2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Periodes\Pipelines
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
function periodes_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['periodes'] = 'periodes';

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
function periodes_declarer_tables_objets_sql($tables) {

	$tables['spip_periodes'] = array(
		'type' => 'periode',
		'principale' => 'oui',
		'field'=> array(
			'id_periode'         => 'bigint(21) NOT NULL',
			'titre'              => 'varchar(255) NOT NULL DEFAULT ""',
			'descriptif'         => 'text NOT NULL',
			'type'               => 'varchar(20) NOT NULL DEFAULT ""',
			'date_complete'      => 'varchar(3) NOT NULL DEFAULT ""',
			'criteres'           => 'varchar(10) NOT NULL DEFAULT ""',
			'operateur'          => 'varchar(3) NOT NULL DEFAULT ""',
			'operateur_2'        => 'varchar(3) NOT NULL DEFAULT ""',
			'date_debut'         => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'date_fin'           => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'jour_debut'         => 'int(1) NOT NULL DEFAULT "0"',
			'jour_fin'           => 'int(1) NOT NULL DEFAULT "0"',
			'jour_nombre'        => 'int(11) NOT NULL DEFAULT "0"',
			'date_debut_jour'               => 'varchar(2) NOT NULL DEFAULT ""',
			'date_debut_mois'               => 'varchar(2) NOT NULL DEFAULT ""',
			'date_debut_annee'              => 'varchar(4) NOT NULL DEFAULT ""',
			'date_fin_jour'               => 'varchar(2) NOT NULL DEFAULT ""',
			'date_fin_mois'               => 'varchar(2) NOT NULL DEFAULT ""',
			'date_fin_annee'              => 'varchar(4) NOT NULL DEFAULT ""',
			'date'               => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_periode',
		),
		'titre' => 'titre AS titre, "" AS lang',
		'date' => 'date',
		'champs_editables'  => array(
			'titre',
			'descriptif',
			'type',
			'date_complete',
			'criteres',
			'operateur',
			'operateur_2',
			'date_debut',
			'date_fin',
			'jour_debut',
			'jour_fin',
			'jour_nombre',
			'date_debut_jour',
			'date_debut_mois',
			'date_debut_annee',
			'date_fin_jour',
			'date_fin_mois',
			'date_fin_annee',
		),
		'champs_versionnes' => array(
			'titre',
			'descriptif',
			'type',
			'date_complete',
			'criteres',
			'operateur',
			'operateur_2',
			'date_debut',
			'date_fin',
			'jour_debut',
			'jour_fin',
			'jour_nombre',
			'date_debut_jour',
			'date_debut_mois',
			'date_debut_annee',
			'date_fin_jour',
			'date_fin_mois',
			'date_fin_annee',
		),
		'rechercher_champs' => array(
			"titre" => 8,
			"descriptif" => 5,
		),
		'tables_jointures'  => array(),
	);

	return $tables;
}
