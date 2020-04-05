<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function depublie_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_depublies')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function depublie_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_depublies");
	effacer_meta($nom_meta_base_version);
}


