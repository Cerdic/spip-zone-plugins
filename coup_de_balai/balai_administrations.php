<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


function balai_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_balai')),
	);

	$maj['0.2.0'] = array(
		array('maj_tables', array('spip_balai')),
		array('sql_alter', "TABLE spip_balai DROP protection_permanente"),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


function balai_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_balai");
	effacer_meta($nom_meta_base_version);
}
?>
