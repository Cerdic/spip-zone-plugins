<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function fusion_spip_upgrade($nom_meta_base_version, $version_cible) {

	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_fusion_spip')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function fusion_spip_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_fusion_spip");
	effacer_meta($nom_meta_base_version);
}
