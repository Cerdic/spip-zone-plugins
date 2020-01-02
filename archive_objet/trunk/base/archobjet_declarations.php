<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclaration des nouvelles tables de la base de données propres au plugin.
 *
 * Le plugin ne déclare aucune nouvelle table mais ajoute des champs aux tables d'objet :
 *
 * - `est_archive`, 0 pour non archivé, 1 pour archivé,
 * - `date_archive`, la date d'archive ou la date de fin d'archive suivant l'état précédent,
 * - `raison_archive`, identifiant unique pour une raison (les raisons sont propres aux plugins utilisateur).
 *
 * @param array $tables
 *
 * @return array
 */
function archobjet_declarer_tables_objets_sql($tables) {

	// On ajoute la déclaration des 3 champs en une fois pour toutes les tables d'objet.
	$tables[]['field']['est_archive'] = 'tinyint(1) DEFAULT 0 NOT NULL';      // 0 non archivé, 1 archivé
	$tables[]['field']['date_archive'] = 'datetime DEFAULT NULL';             // date de l'archivage ou du désarchivage
	$tables[]['field']['raison_archive'] = "varchar(32) DEFAULT '' NOT NULL"; // identifiant de la raison

	return $tables;
}
