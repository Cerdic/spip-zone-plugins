<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


function coggps_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_cog_communes')));
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function coggps_vider_tables($nom_meta_base_version) {

	sql_alter("TABLE spip_cog_communes DROP lon,lat,zoom,elevation,elevation_moyenne,population,autre_nom");
	effacer_meta($nom_meta_base_version);
}




?>
