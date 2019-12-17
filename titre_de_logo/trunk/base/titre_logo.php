<?php

/**
 * Déclarations relatives à la base de données.
 *
 * @plugin	 Titre de logo
 *
 * @copyright  2015
 * @author	 Arno*
 * @licence	GPL 3
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclaration des alias de tables et filtres automatiques de champs.
 *
 * @pipeline declarer_tables_interfaces
 *
 * @param array $interfaces	Déclarations d'interface pour le compilateur
 *
 * @return array	Déclarations d'interface pour le compilateur
 */
function titre_logo_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_traitements']['TITRE_LOGO'][] = _TRAITEMENT_TYPO;
	$interfaces['table_des_traitements']['DESCRIPTIF_LOGO'][] = _TRAITEMENT_RACCOURCIS;

	return $interfaces;
}

/**
 * Declaration des champs sur les objets.
 *
 * @param array $tables
 *
 * @return array
 */
function titre_logo_declarer_tables_objets_sql($tables) {
	// champs titre_logo et descriptif_logo sur les objets configurés
	include_spip('inc/config');
	$tables_logo = lire_config('titre_logo/objets_autorises', array());
	if (is_array($tables_logo) && count($tables_logo) > 0) {
		foreach ($tables_logo as $table) {
			if (isset($tables[$table])) {
				$tables[$table]['field']['titre_logo'] = "text DEFAULT '' NOT NULL";
				$tables[$table]['field']['descriptif_logo'] = "text DEFAULT '' NOT NULL";
			}
		}
	}
	return $tables;
}


/**
 * Fournir la liste des tables objets existantes sur lesquelles on a active les titre_logo
 * écarter les tables connues pour lesquelles c'est inutile
 * @return array
 */
function titre_logo_liste_tables() {
	include_spip('base/objets');
	$tables_objets	 = array_keys(lister_tables_objets_sql());
	$black_liste	   = titre_logo_black_list();

	$tables_logo = lire_config('titre_logo/objets_autorises', array('spip_articles'));

	// uniquement les objets existants
	$tables_logo = array_intersect($tables_logo, $tables_objets);

	// et hors les exclusions
	$tables_logo = array_diff($tables_logo, $black_liste);

	return $tables_logo;
}


/**
 * Black list : les tables connues pour lesquelles il est inutile de fournir les champs 'titre_logo' et 'descriptif_logo'
 * @return array
 */

function titre_logo_black_list() {
	$black_list = array(0 => 'spip_depots',
						1 => 'spip_documents',
						2 => 'spip_forum',
						3 => 'spip_messages',
						4 => 'spip_paquets',
						5 => 'spip_petitions',
						6 => 'spip_plugins',
						7 => 'spip_signatures',
						8 => 'spip_syndic_articles');
	return $black_list;
}
