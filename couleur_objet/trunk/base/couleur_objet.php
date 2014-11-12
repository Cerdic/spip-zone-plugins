<?php
/**
 * Déclaration des tables secondaires (liaisons)
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function couleur_objet_declarer_tables_auxiliaires($tables) {

	$tables['spip_couleur_objet_liens'] = array(
		'field' => array(
			"objet"        => "VARCHAR (25) DEFAULT '' NOT NULL",
			"id_objet"     => "bigint(21) DEFAULT '0' NOT NULL",
			"couleur_objet"      => "VARCHAR (25) DEFAULT '' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"  => "objet,id_objet,couleur_objet",
		)
	);

	return $tables;
}

