<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function connecteur_upgrade($nom_meta_base_version, $version_cible) {

	$maj = array();
	$maj['create'] = array(array('maj_tables', array('spip_connecteur')));
	$maj['1.0.1'] = array(array('maj_tables', array('spip_connecteur')));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function connecteur_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}
