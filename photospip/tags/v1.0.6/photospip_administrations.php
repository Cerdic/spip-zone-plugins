<?php
/**
 * PhotoSPIP
 * Modification d'images dans SPIP
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info -  http://www.kent1.info)
 *
 * © 2008-2015 - Distribue sous licence GNU/GPL
 * Pour plus de details voir le fichier COPYING.txt
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

function photospip_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	
	$maj = array();
	
	$maj['create'] = array(
		array('maj_tables',array('spip_documents_inters')),
		array('config_palette')
	);
	
	$maj['0.2'] = array(
		array('maj_tables',array('spip_documents_inters'))
	);
	
	$maj['0.3'] = array(
		array('maj_tables',array('spip_documents_inters'))
	);
	
	$maj['0.4'] = array(
		array('maj_tables',array('spip_documents_inters')),
		array('config_palette')
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function config_palette(){
	include_spip('inc/config');
	$config_palette = lire_config('palette',array());
	$config_palette['palette_ecrire'] = 'on';
	ecrire_config("palette",$config_palette);
}
function photospip_vider_tables($nom_meta_base_version) {
	include_spip('base/abstract_sql');
	spip_query("DROP TABLE spip_documents_inters");
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}

?>