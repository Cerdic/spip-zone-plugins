<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function mailsubscribers_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	include_spip("inc/mailsubscribers");

	$maj['create'] = array(
		array('maj_tables', array('spip_mailsubscribers'))
	);


	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function mailsubscribers_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_mailsubscribers");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('mailsubscriber')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('mailsubscriber')));
	sql_delete("spip_forum",                 sql_in("objet", array('mailsubscriber')));

	effacer_meta($nom_meta_base_version);
}

?>