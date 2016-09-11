<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Installation/maj
 * On ajoute le champ virtuel à la table spip_articles
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function rubriques_virtuelles_upgrade($nom_meta_base_version, $version_cible) {

	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_rubriques'))
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Desinstallation/suppression
 * On enlève le champ virtuel
 *
 * @param string $nom_meta_base_version
 */
function rubriques_virtuelles_vider_tables($nom_meta_base_version) {
	sql_alter('TABLE spip_rubriques DROP virtuel');
	effacer_meta($nom_meta_base_version);
}
