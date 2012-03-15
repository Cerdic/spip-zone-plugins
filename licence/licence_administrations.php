<?php
/**
 * Plugin Licence
 *
 * (c) 2007-2012 fanouch
 * Distribue sous licence GPL
 *
 * Modification des tables
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

function licence_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	
	$maj['create'] = array(
		array('maj_tables',array('spip_articles')),
		array('maj_tables',array('spip_documents'))
	);
	
	$maj['0.2.0'] = array('maj_tables',array('spip_documents'));
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


function licence_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}

?>