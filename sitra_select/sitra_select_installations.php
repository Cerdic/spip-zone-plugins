<?php
include_spip('inc/meta');
include_spip('base/create');

function sitra_select_upgrade($nom_meta_base_version, $version_cible){
	$current_version = '1.0';
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version == '1.0') {
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version = $version_cible);
	}
}

function sitra_select_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_sitra_select_articles',true);
	effacer_meta('sitra_select_config');
	effacer_meta($nom_meta_base_version);
}
?>