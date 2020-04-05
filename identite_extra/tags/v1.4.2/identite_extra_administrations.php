<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/meta');
include_spip('base/create');

function identite_extra_upgrade($nom_meta_base_version, $version_cible) {
	include_spip('base/upgrade');
	$maj = array();

	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function identite_extra_vider_tables($nom_meta_base_version) {
	// on efface la meta de configuration du plugin
	effacer_meta('identite_extra');
	
	// Supprimer la meta de version du plugin
	effacer_meta($nom_meta_base_version);
}
