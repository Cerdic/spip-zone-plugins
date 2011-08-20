<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


function chats_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_chats')),
	);
	$maj['1.2.0'] = array(
		array('maj_tables', array('spip_chats')),
		array('sql_alter',  "TABLE spip_chats ADD INDEX id_rubrique(id_rubrique)")
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function chats_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_chats");
	effacer_meta($nom_meta_base_version);
}

?>
