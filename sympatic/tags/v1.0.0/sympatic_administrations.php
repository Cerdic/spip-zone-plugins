<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function sympatic_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_sympatic_listes', 'spip_sympatic_abonnes')),
	);

	$maj['0.3.0'] = array(
		array('maj_tables', array('spip_sympatic_listes')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function sympatic_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_sympatic_listes');
	sql_drop_table('spip_sympatic_abonnes');
	effacer_meta($nom_meta_base_version);
}
