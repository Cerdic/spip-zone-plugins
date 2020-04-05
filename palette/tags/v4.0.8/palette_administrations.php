<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Installation du Plugin Palette
 */
function palette_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	$maj['create'] = array(
		array('palette_installer_config'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);

}

/**
 * Enregistrer la configuration par défaut de Palette.
 */
function palette_installer_config() {
	include_spip('inc/config');
	if (is_null(lire_config('palette/palette_public'))) {
		ecrire_config('palette/palette_public','');
	}
	if (is_null(lire_config('palette/palette_ecrire'))) {
		ecrire_config('palette/palette_ecrire', 'on');
	}
}

/**
 * Suppression du Plugin Palette
 * Enlever la config
 */
function palette_vider_tables($nom_meta_base_version) {
	effacer_meta('palette');
	effacer_meta($nom_meta_base_version);
}
