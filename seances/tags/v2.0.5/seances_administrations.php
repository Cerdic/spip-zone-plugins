<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function seances_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();
	$maj['create']	= array(
		array('maj_tables', array('spip_seances', 'spip_seances_endroits')),
		array('sql_alter', 'TABLE spip_rubriques ADD seance tinyint(1) DEFAULT 0 NOT NULL')
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function seances_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_seances_endroits',true);
	sql_drop_table('spip_seances',true);
	sql_alter('TABLE spip_rubriques DROP COLUMN seance');
	effacer_meta($nom_meta_base_version);
}
?>