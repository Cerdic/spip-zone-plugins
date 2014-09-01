<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

// Installation et mise à jour
function sjcycle_upgrade($nom_meta_version_base, $version_cible){

	include_spip('inc/config');
	$maj = array();
	$maj['create'] = array(
			array('ecrire_config','sjcycle', array(
				'largeurmax' => '640',
				'timeout' => '4000',
				'speed' => '1000',
				'fx' => 'fade',
				'afficher_aide' => 'oui'
	)));
	include_spip('base/upgrade');
	maj_plugin($nom_meta_version_base, $version_cible, $maj);
}

// Désinstallation
function sjcycle_vider_tables($nom_meta_version_base){
	effacer_meta('sjcycle');
	effacer_meta($nom_meta_version_base);
}
