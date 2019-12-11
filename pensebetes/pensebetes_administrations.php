<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


function pensebetes_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_pensebetes', 'spip_pensebetes_liens')),
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function pensebetes_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_pensebetes");
	sql_drop_table("spip_pensebetes_liens");
	effacer_meta($nom_meta_base_version);
}

