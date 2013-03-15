<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclarer la table assemblage
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function assemblage_declarer_tables_principales($tables) {

	$tables['spip_assemblage'] = array(
		'principale' => 'oui',
		'field' => array(
			'id_assemblage' => 'bigint(21) NOT NULL',
			'site_origine' => 'varchar(25)',
			'id_origine' => 'bigint(21) NOT NULL',
			'id_final' => 'bigint(21) NOT NULL',
			'objet' => 'varchar(25)',
		),
		'key' => array(
			'PRIMARY KEY' => 'id_assemblage',
			'KEY site_origine'	=> 'site_origine',
			'KEY id_origine'	=> 'id_origine',
			'KEY id_final'	=> 'id_final',
			'KEY objet'	=> 'objet',
		),
	);

	return $tables;
}
