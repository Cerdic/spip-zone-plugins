<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Info Sites
 * @copyright  2014-2019
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\Pipelines
 */

if (!defined("_ECRIRE_INC_VERSION")) {
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
function info_sites_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['projets_references'] = 'projets_references';

	return $interfaces;
}

/**
 * Déclarer les objets éditoriaux pour Info Sites
 *
 * @pipeline declarer_tables_objets_sql
 *
 * @param array $tables
 *     Description des tables
 *
 * @return array
 *     Description complétée des tables
 */
function info_sites_declarer_tables_objets_sql($tables) {
	// De nouveaux rôles pour les auteurs.
	$tables['spip_auteurs']['roles_titres']['dir_projets'] = 'info_sites:dir_projets_label';
	$tables['spip_auteurs']['roles_titres']['chef_projets'] = 'info_sites:chef_projets_label';
	$tables['spip_auteurs']['roles_titres']['commercial'] = 'info_sites:commercial_label';
	$tables['spip_auteurs']['roles_titres']['ref_tech'] = 'info_sites:ref_tech_label';
	$tables['spip_auteurs']['roles_titres']['architecte'] = 'info_sites:architecte_label';
	$tables['spip_auteurs']['roles_titres']['lead_developpeur'] = 'info_sites:lead_developpeur_label';
	$tables['spip_auteurs']['roles_titres']['developpeur'] = 'info_sites:developpeur_label';
	$tables['spip_auteurs']['roles_titres']['integrateur'] = 'info_sites:integrateur_label';

	$tables['spip_auteurs']['roles_objets']['projets'] = array(
		'choix' => array(
			'dir_projets',
			'chef_projets',
			'commercial',
			'ref_tech',
			'architecte',
			'lead_developpeur',
			'developpeur',
			'integrateur',
		),
		'defaut' => 'chef_projets',
	);
	$tables['spip_projets_references'] = array(
		'type' => 'projets_reference',
		'principale' => 'oui',
		'page' => 'projets_reference',
		'table_objet_surnoms' => array('projetsreference'), // table_objet('projets_reference') => 'projets_references'
		'field'=> array(
			'id_projets_reference' => 'bigint(21) NOT NULL',
			'nom'                => 'text NOT NULL DEFAULT ""',
			'url_site'           => 'varchar(255) NOT NULL DEFAULT ""',
			'organisation'       => 'text NOT NULL DEFAULT ""',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_projets_reference',
		),
		'titre' => 'nom AS titre, "" AS lang',
		#'date' => '',
		'champs_editables'  => array('nom', 'url_site', 'organisation'),
		'champs_versionnes' => array('nom', 'url_site', 'organisation'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array('spip_projets_references_liens'),
	);

	return $tables;
}

/**
 * Déclaration des tables secondaires (liaisons)
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function info_sites_declarer_tables_auxiliaires($tables) {

	$tables['spip_projets_references_liens'] = array(
		'field' => array(
			'id_projets_reference' => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet'           => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet'              => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'vu'                 => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_projets_reference,id_objet,objet',
			'KEY id_projets_reference' => 'id_projets_reference',
		)
	);

	return $tables;
}
