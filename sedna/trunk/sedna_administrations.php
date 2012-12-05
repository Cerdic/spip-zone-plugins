<?php
/*
 * Plugin Notifications
 * (c) 2009-2012 SPIP
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Declarer le champ notification_email sur la table forum
 *
 * @param array $tables
 * @return array
 */
function sedna_declarer_tables_objets_sql($tables){
	$tables['spip_auteurs']['field']['sedna'] = "TEXT NOT NULL DEFAULT ''";
	$tables['spip_auteurs']['champs_editables'][] = 'sedna';

	return $tables;
}


/**
 * maj de table auteur en ajoutant le champ sedna
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function sedna_upgrade($nom_meta_base_version,$version_cible){

	$maj = array();
	$maj['create'] = array(
		array('maj_tables',array('spip_auteurs')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function sedna_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}
