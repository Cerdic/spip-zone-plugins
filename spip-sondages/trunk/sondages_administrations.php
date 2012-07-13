<?php
/**
 * Plugin Spip-sondages
 * (c) 2012 Maïeul Rouquette d&#039;après Artego
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function sondages_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_sondages', 'spip_sondages_liens', 'spip_choix', 'spip_avis')));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function sondages_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_sondages");
	sql_drop_table("spip_sondages_liens");
	sql_drop_table("spip_choix");
	sql_drop_table("spip_avis");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('sondage', 'choix', 'avi')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('sondage', 'choix', 'avi')));
	sql_delete("spip_forum",                 sql_in("objet", array('sondage', 'choix', 'avi')));

	effacer_meta($nom_meta_base_version);
}

?>