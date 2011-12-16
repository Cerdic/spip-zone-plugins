<?php

include_spip('base/create');

function svp_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();

	$maj['create'][] = array('maj_tables', array('spip_depots','spip_plugins','spip_depots_plugins','spip_paquets'));
	$maj['0.2'][]    = array('maj_tables', 'spip_paquets');
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function svp_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_depots");
	sql_drop_table("spip_plugins");
	sql_drop_table("spip_depots_plugins");
	sql_drop_table("spip_paquets");
	effacer_meta($nom_meta_base_version);

	spip_log('DESINSTALLATION BDD', 'svp_actions.' . _LOG_INFO);
}

?>
