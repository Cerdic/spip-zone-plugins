<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

// Installation et mise à jour
function forumsectorise_upgrade($nom_meta_version_base, $version_cible){

	$maj = array();

	include_spip('inc/config');
   $maj = array();
	$maj['create'] = array(
			array('ecrire_config','forumsectorise', array(
						'ident_secteur' => array(),
						'type' => 'non',
						'option' => 'tous'
	)));
	$maj['0.3'] = array(
			array('ecrire_config','forumsectorise/ident_secteur', lire_config('forumsectorise/id_secteur')),
			array('effacer_config','forumsectorise/id_secteur')
	);

	include_spip('base/upgrade');
   maj_plugin($nom_meta_version_base, $version_cible, $maj);
}

// Désinstallation
function forumsectorise_vider_tables($nom_meta_version_base){

   effacer_meta('forumsectorise');
	effacer_meta($nom_meta_version_base);
}

?>
