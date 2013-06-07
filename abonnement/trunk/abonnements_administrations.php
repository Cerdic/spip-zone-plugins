<?php
/**
 * Plugin Abonnements
 * (c) 2012 Les Développements Durables
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function abonnements_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_abonnements_offres', 'spip_abonnements_offres_liens', 'spip_abonnements', 'spip_abonnements_offres_notifications')));
	
	// Ajout de la config des notifications
	$maj['2.1.0'] = array(
		array('maj_tables', array('spip_abonnements_offres_notifications'))
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function abonnements_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_abonnements_offres");
	sql_drop_table("spip_abonnements_offres_liens");
	sql_drop_table("spip_abonnements");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('abonnements_offre', 'abonnement')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('abonnements_offre', 'abonnement')));
	sql_delete("spip_forum",                 sql_in("objet", array('abonnements_offre', 'abonnement')));

	effacer_meta($nom_meta_base_version);
}

?>
