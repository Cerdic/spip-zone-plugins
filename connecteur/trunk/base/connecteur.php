<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclarer une table auxiliaires pour stocker les tokens des réseaux sociaux
 *
 * @param array $tables_auxiliaires
 * @access public
 * @return aray
 */
function connecteur_declarer_tables_auxiliaires($tables_auxiliaires) {

	$tables_auxiliaires['spip_connecteur'] = array(
		'field' => array(
			'id_connecteur' => 'bigint(21) NOT NULL',
			'id_auteur' => 'bigint(21) NOT NULL',
			'type' => "varchar(25) DEFAULT '' NOT NULL",
			'token' => 'blob NOT NULL'
		),
		'key' => array(
			'PRIMARY KEY' => 'id_connecteur',
			'KEY id_auteur' => 'id_auteur',
			'KEY type' => 'type'
		)
	);

	return $tables_auxiliaires;
}
