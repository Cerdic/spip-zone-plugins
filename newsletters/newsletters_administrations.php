<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function newsletters_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_newsletters', 'spip_newsletters_liens'))
	);

	$maj['0.1.1'] = array(
		array('sql_alter', "table spip_newsletters ADD baked tinyint NOT NULL DEFAULT 0"),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function newsletters_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_newsletters");
	sql_drop_table("spip_newsletters_liens");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('newsletter')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('newsletter')));
	sql_delete("spip_forum",                 sql_in("objet", array('newsletter')));

	effacer_meta($nom_meta_base_version);
}

?>