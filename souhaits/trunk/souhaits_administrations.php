<?php
/**
 * Plugin À vos souhaits
 * (c) 2012 RastaPopoulos
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function souhaits_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_souhaits')));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function souhaits_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_souhaits");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('souhait')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('souhait')));
	sql_delete("spip_forum",                 sql_in("objet", array('souhait')));

	effacer_meta($nom_meta_base_version);
}

?>