<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function lazysizes_upgrade($nom_meta_base_version, $version_cible) {

	// Création du tableau des mises à jour.
	$maj = array();

	$config = array(
		'options'=>array(
			'css' => 'on',
			'custom_media'=>"--small|(max-width: 480px)\n--medium|(max-width: 800px)"
		),
		'addons'=>array()
	);

	// Tableau de la configuration par défaut
	$maj['0.0.1'] = array(
		array('ecrire_config', 'lazysizes', $config)
	);

	// Maj du plugin.
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/*
 *   Désintaller
 */
function lazysizes_vider_tables($nom_meta_base_version) {
	// Supprimer les méta, ou oublie pas celle de la base.
	effacer_meta('lazysizes');
	effacer_meta($nom_meta_base_version);
}
