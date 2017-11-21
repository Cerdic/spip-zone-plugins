<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function stocks_declarer_tables_interfaces($interface) {
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['stocks'] = 'stocks';
	$interface['tables_jointures']['spip_produits'][] = 'stocks';

	return $interface;
}

function stocks_declarer_tables_objets_sql($tables) {

	$tables['spip_stocks'] = array(
		'type' => 'stock',
		'principale' => 'oui',
		'field' => array(
			'id_stock' => 'bigint(21) NOT NULL',
			'id_objet' => 'bigint(21) NOT NULL DEFAULT 0',
			'objet' => 'varchar(255) NOT NULL DEFAULT ""',
			'quantite' => 'bigint(21) NOT NULL',
			'maj' => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY' => 'id_stock',
			'KEY id_objet' => 'id_objet, objet'
		),
		'champs_editables' => array('quantite', 'objet', 'id_objet'),
		'champs_versionnes' => array('quantite', 'objet', 'id_objet')
	);

	return $tables;
}
