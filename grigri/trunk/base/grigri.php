<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


function grigri_declarer_tables_principales($tables_principales){
	$tables_principales['spip_articles']['field']['grigri']     = "varchar(30) NOT NULL DEFAULT ''";
	$tables_principales['spip_auteurs']['field']['grigri']      = "varchar(30) NOT NULL DEFAULT ''";
	$tables_principales['spip_documents']['field']['grigri']    = "varchar(30) NOT NULL DEFAULT ''";
	$tables_principales['spip_groupes_mots']['field']['grigri'] = "varchar(30) NOT NULL DEFAULT ''";
	$tables_principales['spip_mots']['field']['grigri']         = "varchar(30) NOT NULL DEFAULT ''";
	$tables_principales['spip_rubriques']['field']['grigri']    = "varchar(30) NOT NULL DEFAULT ''";

	return $tables_principales;
}
