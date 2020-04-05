<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


function pmb_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_auteurs_pmb')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function pmb_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_auteurs_pmb");
	effacer_meta("spip_pmb");
	effacer_meta($nom_meta_base_version);
}

