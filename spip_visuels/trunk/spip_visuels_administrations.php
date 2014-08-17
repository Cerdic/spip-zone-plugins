<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function spip_visuels_upgrade($nom_meta_base_version, $version_cible) {
	$config = charger_fonction('config','inc');

	$maj = array();
	$maj['create'] = array(
		array('maj_tables',array('spip_visuels','spip_visuels_liens')),
		array($config),
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function spip_visuels_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}
