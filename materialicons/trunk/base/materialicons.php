<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Déclaration des tables secondaires (liaisons)
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function materialicons_declarer_tables_auxiliaires($tables) {

	$tables['spip_materialicons_liens'] = array(
		'field' => array(
			"objet"        => "VARCHAR (25) DEFAULT '' NOT NULL",
			"id_objet"     => "bigint(21) DEFAULT '0' NOT NULL",
			"style"        => "VARCHAR (25) DEFAULT '' NOT NULL",
			"categorie"    => "VARCHAR (25) DEFAULT '' NOT NULL",
			"icone"        => "VARCHAR (35) DEFAULT '' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"  => "objet,id_objet,icone",
		)
	);

	return $tables;
}

