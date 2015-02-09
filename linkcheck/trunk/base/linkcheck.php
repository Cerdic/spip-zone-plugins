<?php
/**
 * Plugin LinkCheck
 * (c) 2013 Benjamin Grapeloux, Guillaume Wauquier
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function linkcheck_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['linkchecks'] = 'linkchecks';
	$interfaces['table_date']['linkchecks']='date';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function linkcheck_declarer_tables_principales($tables_principales) {

	$spip_linkchecks = array(
			"id_linkcheck"       => "bigint(21) NOT NULL",
			"url"                => "text NOT NULL DEFAULT ''",
			"distant"            => "boolean",
			"etat"               => "varchar(10) NOT NULL DEFAULT ''",
			"code"				 => "varchar(10) NOT NULL DEFAULT ''",
			"essais"			 => "int(1) DEFAULT 0",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"maj"                => "TIMESTAMP");
	$spip_linkchecks_key = array(
			"PRIMARY KEY"        => "id_linkcheck");

	$tables_principales['spip_linkchecks']	=
		array('field' => &$spip_linkchecks, 'key' => &$spip_linkchecks_key);

	return $tables_principales;
}


/**
 * Déclaration des tables secondaires (liaisons)
 */
function linkcheck_declarer_tables_auxiliaires($tables) {

	$tables['spip_linkchecks_liens'] = array(
		'field' => array(
			"id_linkcheck"       => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_linkcheck,id_objet,objet",
			"KEY id_linkcheck"   => "id_linkcheck"
		)
	);

	return $tables;
}


?>
