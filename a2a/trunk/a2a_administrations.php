<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function a2a_upgrade($nom_meta_base_version,$version_cible){
	
	$maj = array();
	
	$maj['create'] = array(
		array('creer_base'),
	);
	
	$maj['0.2.0'] = array(array('maj_tables',array('spip_articles_lies')));
	$maj['0.3.0'] = array(array('sql_alter',array('TABLE spip_articles_lies CHANGE rang rang bigint(21) NOT NULL DEFAULT "0"')));
	$maj['0.4.0'] = array(array('maj_tables',array('spip_articles_lies')));
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function a2a_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_articles_lies");
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}

?>
