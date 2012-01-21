<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

// Installation et mise à jour
function jquerymasonry_upgrade($nom_meta_version_base, $version_cible){

	$maj = array();

	include_spip('inc/config');
   $maj = array();
	$maj['create'] = array(
			array('ecrire_config','jquerymasonry', array(
				'nombre' => '0'
	)));
	include_spip('base/upgrade');
   maj_plugin($nom_meta_version_base, $version_cible, $maj);
}

// Désinstallation
function jquerymasonry_vider_tables($nom_meta_version_base){

   effacer_meta('jquerymasonry');
	effacer_meta($nom_meta_version_base);
}

?>
