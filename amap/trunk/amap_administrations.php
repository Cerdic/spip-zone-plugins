<?php
/**
* Plugin Amap
*
* @author: Stephane Moulinet
* @author: E-cosystems
* @author: Pierre KUHN 
*
* Copyright (c) 2010-2013
* Logiciel distribue sous licence GPL.
*
**/

include_spip('base/amap_tables');
include_spip('inc/meta');
include_spip('inc/cextras');

function amap_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();
	$maj['create'] = array(
			array('maj_tables', array('spip_amap_disponibles','spip_amap_livraisons','spip_amap_paniers','spip_amap_responsables')),
	);
	cextras_api_upgrade(amap_declarer_champs_extras(), $maj['create']);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function amap_vider_tables($nom_meta_base_version){
	//supprimer toutes les tables
	sql_drop_table('spip_amap_disponibles');
	sql_drop_table('spip_amap_livraisons');
	sql_drop_table('spip_amap_paniers');
	sql_drop_table('spip_amap_responsables');
	//suppression des champs supplementaire
	cextras_api_vider_tables(amap_declarer_champs_extras());
	effacer_meta($nom_meta_base_version);
}
?>
