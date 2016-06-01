<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function oembed_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	$maj['create'] = array(
		array('sql_alter',"TABLE spip_documents ADD oembed text NOT NULL DEFAULT ''"),
	);

	// toujours un update des nouveaux providers sur la version cible
	$maj[$version_cible] = array(
		array('sql_drop_table', 'spip_oembed_providers'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function oembed_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_oembed_providers');
	effacer_meta($nom_meta_base_version);
}
