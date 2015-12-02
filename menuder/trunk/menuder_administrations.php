<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function menuder_upgrade($nom_meta_base_version, $version_cible)
{
	$maj = array();
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
	spip_log('Install/upgrade du plugin ('.$nom_meta_base_version.' -> '.$version_cible.')', 'menuder');
}

function menuder_vider_tables($nom_meta_base_version)
{
	// Effacer la config
	effacer_meta('menuder');
	effacer_meta($nom_meta_base_version);
	spip_log('Suppression des tables du plugin', 'menuder');
}
