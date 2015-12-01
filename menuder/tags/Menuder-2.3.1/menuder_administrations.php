<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function menuder_upgrade($nom_meta_base_version, $version_cible)
{
	spip_log('Install/upgrade du plugin', 'menuder');
	$maj = array();
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function menuder_vider_tables($nom_meta_base_version)
{
	spip_log('Suppression des tables du plugin', 'menuder');
	// Effacer la config
	effacer_meta('menuder');
}
