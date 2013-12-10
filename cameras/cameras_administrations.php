<?php
if (!defined('_ECRIRE_INC_VERSION')) return;



function cameras_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('cameras')),
	);
	
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
	
}


function cameras_vider_tables($nom_meta_base_version) {
	
	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('camera')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('camera')));
	sql_delete("spip_forum",                 sql_in("objet", array('camera')));
	
	effacer_meta($nom_meta_base_version);
}
?>
