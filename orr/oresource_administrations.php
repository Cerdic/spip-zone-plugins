<?php
/**
 * Plugin ORR
 * (c) 2012 tofulm
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function oresource_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_orr_ressources', 'spip_orr_reservations', 'spip_orr_reservations_liens')));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function oresource_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_orr_ressources");
	sql_drop_table("spip_orr_reservations");
	sql_drop_table("spip_orr_reservations_liens");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('orr_ressource', 'orr_reservation')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('orr_ressource', 'orr_reservation')));
	sql_delete("spip_forum",                 sql_in("objet", array('orr_ressource', 'orr_reservation')));

	effacer_meta($nom_meta_base_version);
}

?>