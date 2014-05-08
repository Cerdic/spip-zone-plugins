<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Installation/maj du plugin
 *
 * Crée les champs archive_date sur les articles et sur les rubriques
 * 
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function archive_upgrade($nom_meta_base_version,$version_cible){

	$maj = array();
	
	$maj['create'] = array(
		array('maj_tables',array('spip_articles','spip_rubriques'))
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Désinstallation du plugin
 * 
 * Supprime les champs archive_date des articles et des rubriques
 * 
 * @param string $nom_meta_base_version
 */
function archive_vider_tables($nom_meta_base_version) {
	sql_alter('TABLE spip_articles DROP COLUMN archive_date');
	sql_alter('TABLE spip_rubriques DROP COLUMN archive_date');
	effacer_meta('archive');
	effacer_meta($nom_meta_base_version);
}
?>
