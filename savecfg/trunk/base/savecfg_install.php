<?php

/**
 * savecfg.
 *
 * Copyright (c) 2009-2015
 * Yohann Prigent (potter64)
 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
 * Pour plus de details voir le fichier COPYING.txt.
 **/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function savecfg_declarer_tables_interfaces($interface) {
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['savecfg'] = 'savecfg';

	return $interface;
}

function savecfg_declarer_tables_principales($tables_principales) {
	$spip_savecfg = array(
		'id_savecfg' => 'INT(10) NOT NULL AUTO_INCREMENT',
		'fond' => 'text NOT NULL',
		'valeur' => 'text NOT NULL',
		'titre' => 'text NOT NULL',
		'version' => "VARCHAR(100) NOT NULL DEFAULT '1'",
		'date' => 'DATETIME',
	);

	$spip_savecfg_key = array(
		'PRIMARY KEY' => 'id_savecfg',
	);

	$tables_principales['spip_savecfg'] = array(
		'field' => &$spip_savecfg,
		'key' => &$spip_savecfg_key,
	);

	return $tables_principales;
}
