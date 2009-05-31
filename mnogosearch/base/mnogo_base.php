<?php
// -----------------------------------------------------------------------------
// Declaration des tables mnogosearch
// creation 2/07/2006 pour SPIP 1.9

global $tables_principales;
global $tables_auxiliaires;

//-- Table MNOGOSEARCH ------------------------------------------
$mnogosearch_summary = array(
 		"`hash`"	=> "BIGINT UNSIGNED NOT NULL",
		"resume_resultats"	=> "text DEFAULT '' NOT NULL",
		"total"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"maj"	=> "TIMESTAMP"
		);

$mnogosearch_summary_key = array(
		"PRIMARY KEY"	=> "`hash`");
		
$mnogosearch = array(
 		"`hash`"	=> "BIGINT UNSIGNED NOT NULL",
		"numero"	=> "bigint(21) NOT NULL",
		"titre"	=> "text DEFAULT ''",
		"url"	=> "text DEFAULT ''",
		"points"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"popularite"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"descriptif"	=> "text DEFAULT ''",
		"taille"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"mime_type"	=> "varchar(100) NOT NULL",
		"date"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"cache_url"	=> "text DEFAULT ''",
		"valide"		=> "ENUM('non', 'oui') DEFAULT 'oui' NOT NULL",
		"maj"	=> "TIMESTAMP"
		);

$mnogosearch_key = array(
		"PRIMARY KEY"	=> "`hash`, numero",
		"KEY numero"	=> "numero");


$tables_principales['spip_mnogosearch_summary'] =
	array('field' => &$mnogosearch_summary, 'key' => &$mnogosearch_summary_key);

$tables_principales['spip_mnogosearch'] =
	array('field' => &$mnogosearch, 'key' => &$mnogosearch_key);

// 'spip_' dans l'index de $tables_principales
global $table_des_tables;
$table_des_tables['mnogosearch']='mnogosearch';
$table_des_tables['mnogosearch_summary']='mnogosearch_summary';

?>