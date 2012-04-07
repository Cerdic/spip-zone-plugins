<?php

include_spip('base/create');

function relecture_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();

	$maj['create'][] = array('maj_tables', array('spip_relectures', 'spip_commentaires'));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function relecture_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_relectures');
	sql_drop_table('spip_commentaires');
	effacer_meta($nom_meta_base_version);
}

?>
