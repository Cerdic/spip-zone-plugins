<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Installation/maj du plugin rssarticle
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function rssarticle_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	$maj['create'] = array(
		array('sql_alter', 'TABLE spip_syndic ADD rssarticle varchar(3) DEFAULT "non" NOT NULL'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Desinstallation du plugin rssarticle
 *
 * @param string $nom_meta_base_version
 */
	
function rssarticle_vider_tables($nom_meta_base_version) {
	sql_alter("TABLE spip_syndic DROP COLUMN rssarticle");
	effacer_meta($nom_meta_base_version);
}


