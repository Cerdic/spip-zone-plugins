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
include_spip('amap_fonctions');
include_spip('inc/meta');
include_spip('inc/cextras');

function amap_upgrade($nom_meta_version_base, $version_cible){

	$maj = array();
	$maj['create'] = array(
			array('maj_tables', array('spip_amap_disponibles','spip_amap_livraisons','spip_amap_paniers','spip_amap_responsables')),
			array('amap_rubriques'),
	);
	cextras_api_upgrade(amap_declarer_champs_extras(), $maj['create']);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_version_base, $version_cible, $maj);
}

function amap_rubriques(){
	create_rubrique("000. Agenda de la saison", "0");
	$id_rubrique = id_rubrique("000. Agenda de la saison");
	if ($id_rubrique >0) {
		create_rubrique("001. Distribution", $id_rubrique);
		create_rubrique("002. Événements", $id_rubrique);
	}
	create_rubrique("001. Archives", "0");
}

function amap_vider_tables($nom_meta_version_base){
	//supprimer toutes les tables
	sql_drop_table('spip_amap_disponibles');
	sql_drop_table('spip_amap_livraisons');
	sql_drop_table('spip_amap_paniers');
	sql_drop_table('spip_amap_responsables');
	//suppression des champs supplementaire
	cextras_api_vider_tables(amap_declarer_champs_extras());
	effacer_meta($nom_meta_version_base);
}
?>
