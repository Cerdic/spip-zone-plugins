<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function accesrestreintdate_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['zones_dates'] = 'zones_dates';

	return $interfaces;
}

/*
 * Déclaration des tables principales non objet
 */
function accesrestreintdate_declarer_tables_principales($tables) {
	$tables['spip_zones_dates'] = array(
		'field' => array(
			'id_zones_date'         => 'bigint(21) not null',
			'objet'                 => 'varchar(255) default "" not null',
			'id_objet'              => 'bigint(21) DEFAULT 0 NOT NULL',
			'id_zone'               => 'bigint(21) DEFAULT 0 NOT NULL',
			'quand'                 => 'varchar(25) default "" not null',
			'duree'                 => 'int(11) NOT NULL DEFAULT 0',
			'periode'               => 'varchar(25) NOT NULL DEFAULT ""',
		),
		'key' => array(
			'PRIMARY KEY'           => 'id_zones_date',
			'KEY id_zone'           => 'id_zone',
		),
	);
	
	return $tables;
}

