<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

// Installation et mise à jour
function sjcycle_upgrade($nom_meta_version_base, $version_cible){

	$maj = array();

	include_spip('inc/config');
   $maj = array();
	$maj['create'] = array(
			array('ecrire_config','sjcycle', array(
				'tooltip' => '',
				'tooltip_carac' => '',
				'mediabox' => '',
				'fx' => 'fade',
				'sync' => 'on',
				'speed' => '2000',
				'timeout' => '4000',
				'pause' => '',
				'random' => '',
				'div_class' => 'dsjcycle',
				'div_width' => '400',
				'div_height' => '400',
				'div_margin' => '0',
				'img_bordure' => '0',
				'div_background' => 'ffffff',
				'img_position' => 'center',
				'img_width' => '400',
				'img_height' => '400',
				'img_background' => 'ffffff',
				'afficher_aide' => 'on'
	)));
	include_spip('base/upgrade');
   maj_plugin($nom_meta_version_base, $version_cible, $maj);
}

// Désinstallation
function sjcycle_vider_tables($nom_meta_version_base){

   effacer_meta('sjcycle');
	effacer_meta($nom_meta_version_base);
}

?>
