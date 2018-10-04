<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Rôles de sélections éditoriales
 * @copyright  2018
 * @author     Rôles de sélections éditoriales
 * @licence    GNU/GPL
 * @package    SPIP\Menus\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Déclaration des objets éditoriaux
 *
 * Déclarer la liste des rôles pour les sélections.
 * On n'indique que le rôle par défaut.
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function roles_selections_declarer_tables_objets_sql($tables){

	array_set_merge($tables, 'spip_selections', array(
		'roles_colonne' => 'role',
		'roles_titres' => array(
			'selection' => 'roles_selections:role_selection',
		),
		'roles_objets' => array(
			'*' => array(
				'choix' => array('selection'),
				'defaut' => 'selection'
			)
		)
	));

	return $tables;
}


/**
 * Déclaration des tables secondaires (liaisons)
 *
 * Ajouter la colonne rrole sur les selections
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function roles_selections_declarer_tables_auxiliaires($tables) {

	$tables['spip_selections_liens']['field']['role']      = "varchar(30) NOT NULL DEFAULT ''";
	$tables['spip_selections_liens']['key']['PRIMARY KEY'] = "id_selection,id_objet,objet,role";

	return $tables;
}
