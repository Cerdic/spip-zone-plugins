<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function mailshot_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_mailshots')),
	);
	$maj['0.1.1'] = array(
		array('maj_tables', array('spip_mailshots')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function mailshot_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_mailshots");

	effacer_meta($nom_meta_base_version);
}

?>