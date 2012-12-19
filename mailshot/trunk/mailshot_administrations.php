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
		array('maj_tables', array('spip_mailshots','spip_mailshots_destinataires')),
	);

	$maj['0.1.4'] = array(
		array('maj_tables', array('spip_mailshot')),
	);
	$maj['0.2.0'] = array(
		array('maj_tables', array('spip_mailshot_destinataires')),
	);
	$maj['0.2.1'] = array(
		array('sql_alter', 'TABLE spip_mailshot DROP next'),
	);
	$maj['0.3.0'] = array(
		array('sql_alter', 'TABLE spip_mailshot RENAME spip_mailshots'),
		array('sql_alter', 'TABLE spip_mailshot_destinataires RENAME spip_mailshots_destinataires'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function mailshot_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_mailshots");
	sql_drop_table("spip_mailshots_destinataires");

	effacer_meta($nom_meta_base_version);
}

?>