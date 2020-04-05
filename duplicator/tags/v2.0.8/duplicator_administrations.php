<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin Duplicator.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function duplicator_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	
	// Rien à faire à l'installation
	$maj['create'] = array();
	
	// Pour les anciens qui migrent, on déplace la config
	$maj['1.0.0'] = array(
		array('duplicator_maj_1_0_0'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin Chapitres.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function duplicator_vider_tables($nom_meta_base_version) {
	// Effacer la config
	effacer_meta('duplicator');
	
	effacer_meta($nom_meta_base_version);
}

/**
 * On déplace la config tant que faire se peut
 */
function duplicator_maj_1_0_0() {
	include_spip('inc/config');
	$config = lire_config('duplicator/config');
	$nouvelle_config = array();
	
	// Config des objets
	if (isset($config['duplic_rubrique']) and $config['duplic_rubrique'] == 'oui') {
		$nouvelle_config['objets'][] = 'spip_rubriques';
	}
	if (isset($config['duplic_article']) and $config['duplic_article'] == 'oui') {
		$nouvelle_config['objets'][] = 'spip_articles';
	}
	
	// Config des champs
	if (isset($config['rub_champs']) and $champs = $config['rub_champs']) {
		$champs = explode(',', $champs);
		$champs = array_map('trim', $champs);
		$nouvelle_config['rubrique']['champs'] = $champs;
	}
	if (isset($config['art_champs']) and $champs = $config['art_champs']) {
		$champs = explode(',', $champs);
		$champs = array_map('trim', $champs);
		$nouvelle_config['article']['champs'] = $champs;
	}
	
	// Config des autorisations : non on prend par défaut plutôt, qui n'existait pas avant, donc ça on ne le migre pas
	
	// Config du statut
	if (isset($config['duplic_article_etat_pub']) and $config['duplic_article_etat_pub'] == 'oui') {
		$nouvelle_config['article']['statut'] = '';
	}
	else {
		$nouvelle_config['article']['statut'] = 'prepa';
	}
	
	// Et on enregistre
	ecrire_config('duplicator', $nouvelle_config);
}
