<?php
/**
 * Plugin mailsuscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function mailsuscribers_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	include_spip("inc/mailsuscribers");

	$maj['create'] = array(
		array('maj_tables', array('spip_mailsuscribers'))
	);

	$maj['0.1.1'] = array(
		array('maj_tables', array('spip_mailsuscribers')),
	);
	$maj['0.1.2'] = array(
		array('sql_alter',"TABLE spip_mailsuscribers CHANGE abonnements listes text NOT NULL DEFAULT ''"),
	);
	$maj['0.1.3'] = array(
		array('sql_updateq',"spip_mailsuscribers",array('listes'=>mailsuscribers_normaliser_nom_liste()),"listes=".sql_quote('')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function mailsuscribers_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_mailsuscribers");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('mailsuscriber')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('mailsuscriber')));
	sql_delete("spip_forum",                 sql_in("objet", array('mailsuscriber')));

	effacer_meta($nom_meta_base_version);
}

?>