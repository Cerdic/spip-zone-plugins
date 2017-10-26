<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Swiper
 * @copyright  2017
 * @author     Charles Stephan
 * @licence    GNU/GPL
 * @package    SPIP\Swiper\Pipelines
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
function swiper_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['swipers'] = 'swipers';

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
function swiper_declarer_tables_objets_sql($tables) {

	$tables['spip_swipers'] = array(
		'type' => 'swiper',
		'principale' => 'oui',
		'field'=> array(
			'id_swiper'          => 'bigint(21) NOT NULL',
			'titre'              => 'varchar(255) NOT NULL DEFAULT ""',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_swiper',
		),
		'titre' => 'titre AS titre, "" AS lang',
		 #'date' => '',
		'champs_editables'  => array('titre'),
		'champs_versionnes' => array('titre'),
		'rechercher_champs' => array("titre" => 1),
		'tables_jointures'  => array(),


	);

	return $tables;
}


function swiper_declarer_champs_extras($champs = array()) {

	$champs['spip_documents']['nom_lien'] = array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'nom_lien',
			'label' => _T('swiper:nom_lien'),
			'sql' => "varchar(255) NOT NULL DEFAULT ''",
			'defaut' => '',
		),
	);

	$champs['spip_documents']['url_lien'] = array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'url_lien',
			'label' => _T('swiper:url_lien'),
			'sql' => "varchar(255) NOT NULL DEFAULT ''",
			'defaut' => '',
		),
	);

	return $champs;

}
