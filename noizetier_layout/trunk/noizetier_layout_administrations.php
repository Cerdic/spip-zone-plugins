<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Installation du schéma de données propre au plugin et gestion des migrations suivant
 * les évolutions du schéma.
 *
 * Le schéma comprend des tables et des variables de configuration propres au plugin.
 *
 * @param string $nom_meta_base_version
 * 		Nom de la meta dans laquelle sera rangée la version du schéma
 * @param string $version_cible
 * 		Version du schéma de données en fin d'upgrade
 *
 * @return void
 */
function noizetier_layout_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	// Configurations par défaut
	$config = array(
		'inclure_css_public' => 'on',
	);

	// 1ère installation
	$maj['create'] = array(
		array('ecrire_config', 'noizetier_layout', $config),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Suppression de l'ensemble du schéma de données propre au plugin, c'est-à-dire
 * les tables et les variables de configuration.
 *
 * @param string $nom_meta_base_version
 * 		Nom de la meta dans laquelle sera rangée la version du schéma
 *
 * @return void
 */
function noizetier_layout_vider_tables($nom_meta_base_version) {

	// On efface la version enregistrée du schéma des données du plugin
	effacer_meta($nom_meta_base_version);
	// On efface la configuration du plugin
	effacer_meta('noizetier_layout');
}