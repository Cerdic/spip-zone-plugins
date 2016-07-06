<?php
/**
 * Plugin Intranet
 *
 * (c) 2013-2016 kent1
 * Distribue sous licence GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Installation/maj des tables intranet...
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function intranet_upgrade($nom_meta_base_version, $version_cible) {

	$maj = array();
	$maj['create'] = array( array('maj_tables', array('spip_intranet_ouverts')));
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Desinstallation/suppression des tables intranet
 *
 * @param string $nom_meta_base_version
 */
function intranet_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_intranet_ouverts');

	effacer_meta($nom_meta_base_version);
}
