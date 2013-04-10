<?php
/**
 * Plugin Signalement
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2012 - Distribue sous licence GNU/GPL
 *
 * Installation de signalement
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

function signalement_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	
	$maj['create'] = array(
		array('maj_tables',array('spip_signalements')),
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function signalement_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
	sql_drop_table('spip_signalements');
}
?>