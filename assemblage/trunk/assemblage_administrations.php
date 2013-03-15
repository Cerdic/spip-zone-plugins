<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function assemblage_upgrade($nom_meta_base_version, $version_cible) {

	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_assemblage')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function assemblage_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_assemblage");
	effacer_meta($nom_meta_base_version);
}
