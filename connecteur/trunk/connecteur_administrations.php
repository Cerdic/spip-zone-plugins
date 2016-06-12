<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function connecteur_upgrade($nom_meta_base_version, $version_cible) {

	$maj = array();
	$maj['create'] = array(array('maj_tables', array('spip_connecteur')));
	$maj['1.0.1'] = array(array('maj_tables', array('spip_connecteur')));

	$maj['1.0.2'] = array(
		array('sql_alter', 'TABLE spip_connecteur DROP COLUMN expire')
	);

	$maj['1.0.3'] = array(
		array('sql_alter', 'TABLE spip_connecteur MODIFY token blob NOT NULL')
	);

	$maj['1.0.4'] = array(array('maj_tables', array('spip_connecteur')));

	$maj['1.0.5'] = array(array('maj_tables', array('spip_connecteur')));

	$maj['1.0.9'] = array(
		array('sql_alter', 'TABLE spip_connecteur CHANGE id_connecteur id_connecteur bigint(21) NOT NULL FIRST'),
		array('sql_alter', 'TABLE spip_connecteur DROP primary key'),
		array('sql_alter', 'TABLE spip_connecteur ADD INDEX `id_connecteur` (id_connecteur), MODIFY id_connecteur bigint(21) auto_increment')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function connecteur_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_connecteur');
	effacer_meta($nom_meta_base_version);
}
