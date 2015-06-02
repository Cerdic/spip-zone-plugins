<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


function chats_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_chats', 'spip_chats_liens')),
	);
	// id_rubrique
	$maj['1.2.0'] = array(
		array('maj_tables', array('spip_chats')),
		array('sql_alter',  "TABLE spip_chats ADD INDEX id_rubrique(id_rubrique)")
	);
	// lang, langue_choisie
	$maj['1.3.0'] = array(array('maj_tables', array('spip_chats')));
	// id_trad
	$maj['1.4.0'] = array(array('maj_tables', array('spip_chats')));
	// statut
	$maj['1.5.0'] = array(
		array('maj_tables', array('spip_chats')),
		array('sql_updateq', 'spip_chats', array('statut'=>'publie'))
	);
	// spip_chats_liens
	$maj['1.6.0'] = array(array('maj_tables', array('spip_chats_liens')));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function chats_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_chats");
	sql_drop_table("spip_chats_liens");
	effacer_meta($nom_meta_base_version);
}

