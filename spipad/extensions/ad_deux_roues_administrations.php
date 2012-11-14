<?php
/**
 * Plugin SpipAd - 2roues
 * (c) 2012 Collectif SPIP - Montpellier
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function ad_deux_roues_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_ad_deux_roues')));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function ad_deux_roues_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_ad_deux_roues");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('ad_deux_roue')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('ad_deux_roue')));
	sql_delete("spip_forum",                 sql_in("objet", array('ad_deux_roue')));

	effacer_meta($nom_meta_base_version);
}

?>