<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

// Installation et mise à jour
function jquerysuperfish_upgrade($nom_meta_version_base, $version_cible){

	$maj = array();

	include_spip('inc/config');
   $maj = array();
	$maj['create'] = array(
			array('ecrire_config','jquerysuperfish', array(
				'menu_hori' => '',
				'menu_vert' => '',
				'menu_navbar' => ''
	)));
	include_spip('base/upgrade');
   maj_plugin($nom_meta_version_base, $version_cible, $maj);
}

// Désinstallation
function jquerysuperfish_vider_tables($nom_meta_version_base){

   effacer_meta('jquerysuperfish');
	effacer_meta($nom_meta_version_base);
}

?>
