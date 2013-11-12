<?php
/**
 * Plugin Diogene
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2010-2012 - Distribue sous licence GNU/GPL
 *
 * Installation de diogène
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

function diogene_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	
	$maj['create'] = array(
		array('maj_tables',array('spip_diogenes','spip_diogenes_liens')),
	);
	$maj['0.2'] = array(
		array('maj_tables',array('spip_diogenes','spip_diogenes_liens')),
	);
	$maj['0.3'] = array(
		array('maj_tables',array('spip_diogenes')),
	);
	$maj['0.3.1'] = array(
		array('maj_tables',array('spip_diogenes')),
	);
	$maj['0.3.2'] = array(
		array('maj_tables',array('spip_diogenes')),
	);
	$maj['0.3.3'] = array(
		array('sql_alter',"TABLE spip_diogenes CHANGE id_secteur id_secteur bigint(21) NOT NULL"),
		array('sql_alter',"TABLE spip_diogenes description description mediumtext DEFAULT '' NOT NULL"),
	);
	$maj['0.3.4'] = array(
		array('sql_alter',"TABLE spip_diogenes ADD INDEX id_secteur (id_secteur)"),
		array('sql_alter',"TABLE spip_diogenes ADD INDEX type (type)"),
		array('sql_alter',"TABLE spip_diogenes ADD INDEX objet (objet)")
	);
	$maj['0.3.5'] = array(
		array('sql_alter',"TABLE spip_diogenes CHANGE id_secteur id_secteur bigint(21) DEFAULT '0' NOT NULL")
	);
	$maj['0.3.6'] = array(
		array('maj_tables',array('spip_diogenes'))
	);
	$maj['0.3.7'] = array(
		array('maj_tables',array('spip_diogenes'))
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function diogene_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
	sql_drop_table('spip_diogenes');
	sql_drop_table('spip_diogenes_liens');
}
?>
