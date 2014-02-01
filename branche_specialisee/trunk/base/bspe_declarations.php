<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des tables secondaires (liaisons)
 */
function bspe_declarer_tables_auxiliaires($tables) {

	$tables['spip_branches_specialisees'] = array(
		'field' => array(
			"id_branche"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"objet"			=> "VARCHAR (25) DEFAULT '' NOT NULL",
			"type"			=> "VARCHAR (25) DEFAULT '' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"		=> "id_branche,objet,type",
		)
	);

	return $tables;
}

?>
