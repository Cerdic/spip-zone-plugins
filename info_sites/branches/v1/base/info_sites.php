<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Info Sites
 * @copyright  2014-2016
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\Pipelines
 */

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
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

	return $tables;
}

