<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/meta');

function ap_upgrade($nom_meta, $version_cible) {
	global $tables_principales;
	$current_version = 0.0;
	
	if (   (!isset($GLOBALS['meta'][$nom_meta]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta]) != $version_cible)){
		include_spip('base/ap_serial');
		if ($current_version == 0.0){
			//include_spip('base/create');
			include_spip('base/abstract_sql');
			//on ajoute le champ
			spip_query("ALTER TABLE spip_messages ADD `lieu` TEXT DEFAULT '' NOT NULL AFTER `statut`");
			//parce que creer_base ne sait que creer des tables
			//creer_base();
			ecrire_meta($nom_meta, $current_version = 0.1,'non');
			ecrire_metas();
		}
	}
}

function ap_vider_tables($nom_meta) {
	include_spip('base/ap_serial');
	include_spip('base/abstract_sql');
	spip_query("ALTER TABLE spip_messages DROP lieu");
	effacer_meta($nom_meta);
	ecrire_metas();
}

?>