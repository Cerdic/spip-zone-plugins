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

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/amap_tables');
include_spip('amap_fonctions');
include_spip('inc/meta');
include_spip('inc/cextras');

function amap_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();
	$maj['create'] = array(
			array('maj_tables', array('spip_amap_disponibles','spip_amap_livraisons','spip_amap_paniers','spip_amap_responsables')),
			array('amap_rubriques'),
	);
	$maj['1.2.1'] = array(
			array('maj_tables', array('spip_amap_paniers')),
	);
	cextras_api_upgrade(amap_declarer_champs_extras(), $maj['create']);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function amap_rubriques(){
	create_rubrique("000. Agenda de la saison", "0");
	$id_rubrique = id_rubrique("000. Agenda de la saison");
	if ($id_rubrique >0) {
		create_rubrique("001. Distribution", $id_rubrique);
		create_rubrique("002. Événements", $id_rubrique);
	}
	create_rubrique("001. Archives", "0");
	ecrire_config('amap/email', 'oui');
}

function amap_vider_tables($nom_meta_base_version){
	sql_drop_table('spip_amap_disponibles');
	sql_drop_table('spip_amap_livraisons');
	sql_drop_table('spip_amap_paniers');
	sql_drop_table('spip_amap_responsables');
	cextras_api_vider_tables(amap_declarer_champs_extras());
	effacer_meta('amap_mail');
	effacer_meta($nom_meta_base_version);
}
?>
