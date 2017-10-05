<?php
/**
 * Plugin Fulltext
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function fulltext_upgrade($nom_meta_base_version, $version_cible) {
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
      || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if ($current_version==0.0){
			include_spip('base/create');
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if (version_compare($current_version,'0.2','<')) {
			include_spip('base/abstract_sql');
			sql_alter("TABLE spip_documents CHANGE indexe extrait VARCHAR(3) NOT NULL default 'non'");
			// vider le cache des descriptions de tables
			$trouver_table = charger_fonction('trouver_table', 'base');
			$trouver_table(false);
			ecrire_meta($nom_meta_base_version,$current_version="0.2",'non');
		}
	}
}
?>