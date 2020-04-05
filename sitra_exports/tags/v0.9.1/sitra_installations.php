<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/meta');
include_spip('base/create');

function sitra_upgrade($nom_meta_base_version, $version_cible){
	$current_version = '1.0';
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version == '1.0') {
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version = $version_cible);
	}
}

function sitra_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_sitra_objets',true);
	sql_drop_table('spip_sitra_objets_details',true);
	sql_drop_table('spip_sitra_categories',true);
	sql_drop_table('spip_sitra_docs',true);
	sql_drop_table('spip_sitra_docs_details',true);
	sql_drop_table('spip_sitra_selections',true);
	sql_drop_table('spip_sitra_criteres',true);
	effacer_meta($nom_meta_base_version);
	effacer_meta('sitra_config');
	// effacer_meta('sitra');
}
?>