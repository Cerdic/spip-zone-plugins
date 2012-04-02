<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function abomailmans_upgrade($nom_meta_base_version,$version_cible){
	
	$maj = array();
	
	$maj['create'] = array(
		array('creer_base'),
	);
	
	$maj['0.30'] = array(array('maj_tables',array('spip_abomailmans')));
	$maj['0.31'] = array(array('maj_tables',array('spip_abomailmans')));
	$maj['0.32'] = array(array('maj_tables',array('spip_abomailmans')));
	$maj['0.33'] = array(array('maj_tables',array('spip_abomailmans')));
	$maj['0.34'] = array(array('maj_tables',array('spip_abomailmans')));
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function abomailmans_vider_tables($nom_meta_base_version) {
	include_spip('base/abstract_sql');
	sql_drop_table("spip_abomailmans");
	effacer_meta($nom_meta_base_version);
}

?>