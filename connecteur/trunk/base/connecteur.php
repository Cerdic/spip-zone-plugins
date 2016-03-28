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
			'id_auteur' => 'bigint(21) NOT NULL',
			'type' => "varchar(25) DEFAULT '' NOT NULL",
			'token' => "text NOT NULL DEFAULT ''",
			'expire' => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'"
		),
		'key' => array(
			'PRIMARY KEY' => 'id_auteur',
			'KEY type' => 'type'
		)
	);

	return $tables_auxiliaires;
}
