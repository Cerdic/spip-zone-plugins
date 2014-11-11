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
			"id_objet"     => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"        => "VARCHAR (25) DEFAULT '' NOT NULL",
			"couleur"      => "VARCHAR (25) DEFAULT '' NOT NULL",
			"vu"           => "ENUM('non', 'oui') DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"  => "id_objet,objet,couleur",
		)
	);

	return $tables;
}

