<?php
/**
 * Déclarations relatives à la base de données
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function sms_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['sms_logs'] = 'sms_logs';

	return $interfaces;
}

/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function sms_declarer_tables_objets_sql($tables) {

	$tables['spip_sms_logs'] = array(
		'type'       => 'sms_log',
		'principale' => 'oui',
		'field'=> array(
			'id_sms_log' => 'bigint(21) NOT NULL',
			'nbr_sms'    => 'tinyint(4) NOT NULL',
			'message'    => 'text NOT NULL DEFAULT ""',
			'telephone'  => 'text NOT NULL DEFAULT ""',
			'type_sms'   => 'varchar(255) NOT NULL DEFAULT ""',
			'date'       => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
		),
		'key' => array(
			'PRIMARY KEY' => 'id_sms_log',
		),
	);

	return $tables;
}
