<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

// Installation et mise à jour
function noizetier_upgrade($nom_meta_base_version,$version_cible){

	$version_actuelle = '0.0';

	$maj = array();
	
	$maj['create'] = array(
		array('maj_tables',array('spip_noisettes'))
	);
	
	$maj['0.2'] = array(
		array('maj_tables',array('spip_noisettes'))
	);
	
	$maj['0.3'] = array(
		array('sql_alter',"TABLE spip_noisettes DROP COLUMN contexte")
	);
	
	$maj['0.4'] = array(
		array('maj_tables',array('spip_noisettes'))
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

// Désinstallation
function noizetier_vider_tables($nom_meta_version_base){
	// On efface les tables du plugin
	sql_drop_table('spip_noisettes');
	
	// On efface la version enregistrée
	effacer_meta($nom_meta_version_base);
	
	// On efface les compositions enregistrées
	effacer_meta('noizetier_compositions');
}

?>