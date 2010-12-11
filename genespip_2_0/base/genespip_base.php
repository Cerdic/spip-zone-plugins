<?php

function genespip_upgrade($nom_meta_base_version, $version_cible){
	$current_version = 0.0;
		if ((!isset($GLOBALS['meta'][$nom_meta_base_version])) || (($current_version = $GLOBALS['meta'][$nom_meta_base_version]) != $version_cible)){
		if (version_compare($current_version,'0.0','<=')){
			// Creation des tables
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			genespip_config_site();
			ecrire_meta($nom_meta_base_version, $current_version=$version_cible, 'non');
			}
	}
}

function genespip_vider_tables($nom_meta_base_version){
	//supprimer toutes les tables
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	sql_drop_table('spip_genespip_documents');
	sql_drop_table('spip_genespip_evenements');
	sql_drop_table('spip_genespip_individu');
	sql_drop_table('spip_genespip_journal');
	sql_drop_table('spip_genespip_lieux');
	sql_drop_table('spip_genespip_liste');
	sql_drop_table('spip_genespip_type_evenements');
	effacer_meta($nom_meta_base_version);
	effacer_meta('genespip_plugin_version');
	effacer_meta('genespip_squelette_version');
}
?>
