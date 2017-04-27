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

	// champs titre_logo et descriptif_logo sur tous les objets
	$tables[]['field']['titre_logo'] = "text DEFAULT '' NOT NULL";
	$tables[]['field']['descriptif_logo'] = "text DEFAULT '' NOT NULL";

	return $tables;
}
