<?php
/**
 * Plugin Portfolio/Gestion des documents
 * Licence GPL (c) 2006-2008 Cedric Morin, romy.tetue.net
 *
 */

function types_documents_declarer_tables_interfaces($interface){
	return $interface;
}

function types_documents_declarer_tables_principales($tables_principales){
	
	$tables_principales['spip_types_documents']['field']['interdit'] = "ENUM('oui','non') NOT NULL DEFAULT 'non'";
	return $tables_principales;
}

function types_documents_declarer_tables_auxiliaires($tables_auxiliaires){
	return $tables_auxiliaires;
}

function types_documents_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.1','<')){
			include_spip('base/abstract_sql');
			sql_alter("TABLE spip_types_documents ADD interdit ENUM('oui','non') NOT NULL DEFAULT 'non'");
			ecrire_meta($nom_meta_base_version,$current_version="0.1",'non');
		}
	}
}

?>
