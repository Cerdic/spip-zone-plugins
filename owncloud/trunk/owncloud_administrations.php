<?php
/**
 * Administrations pour owncloud
 *
 * @plugin     owncloud
 * @copyright  2016
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\owncloud\administrations
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/cextras');
include_spip('base/owncloud');

/**
 * Installation/maj des tables owncloud
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function owncloud_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_ownclouds'))
	);

	// Creation du champs md5 dans spip_documents
	cextras_api_upgrade(owncloud_declarer_champs_extras(), $maj['create']);

	$maj['0.1.1'] = array(
		// Suppression de unsigned et auto incremente pour compat sqlite
		array('sql_alter', 'TABLE spip_ownclouds MODIFY id_owncloud BIGINT(21) NOT NULL AUTO_INCREMENT')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Desinstallation/suppression des tables owncloud
 *
 * @param string $nom_meta_base_version
 */
function owncloud_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_ownclouds');
	effacer_meta('owncloud');
	cextras_api_vider_tables(owncloud_declarer_champs_extras());
	unlink(_DIR_TMP . 'owncloud.json');

	effacer_meta($nom_meta_base_version);
}
