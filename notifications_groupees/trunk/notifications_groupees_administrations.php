<?php
/*
 * Plugin Notifications groupees
 * (c) 2013
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function notifications_groupees_declarer_tables_objets_sql($tables){

	// champ notifications_groupees
	// 0/1 (non abonne/abonne) defaut 1
	$tables['spip_forum']['field']['notifications_groupees'] = "tinyint NOT NULL default 1";
	$tables['spip_forum']['champs_editables'][] = 'notifications_groupees';
	$tables['spip_auteurs']['field']['notifications_groupees'] = "tinyint NOT NULL default 1";
	$tables['spip_auteurs']['champs_editables'][] = 'notifications_groupees';

	return $tables;
}

function notifications_groupees_upgrade($nom_meta_base_version,$version_cible){

	$maj = array();
	$maj['create'] = array(
		array('maj_tables',array('spip_forum')),
		array('maj_tables',array('spip_auteurs')),
	);

	$maj['0.1.0'] = array(
		array('maj_tables',array('spip_forum')),
		array('maj_tables',array('spip_auteurs')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function notifications_groupees_vider_tables($nom_meta_base_version) {
	sql_alter("TABLE spip_forum DROP COLUMN notifications_groupees");
	sql_alter("TABLE spip_auteurs DROP COLUMN notifications_groupees");
	effacer_meta($nom_meta_base_version);
}