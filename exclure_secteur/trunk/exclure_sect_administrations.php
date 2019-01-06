<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Exclure secteur.
 * Le schéma de données du plugin consiste uniquement en une configuration meta.
 *
 * @plugin     Exclure secteur
 * @copyright  2013
 * @author     Maïeul Rouquette
 * @licence    GPL 3
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function exclure_sect_upgrade($nom_meta_base_version, $version_cible) {

	// Initialisation du tableau des mises à jour.
	$maj = array();

	// Initialisation des valeurs par défaut.
	$config_defaut = configurer_exclure_sect();

	// Pour la première installation du plugin
	$maj['create'] = array(
		array('ecrire_config', 'secteur', $config_defaut)
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Initialise la configuration du plugin.
 *
 * @return array
 * 		Le tableau de la configuration par défaut qui servira à initialiser la meta `secteur`.
**/
function configurer_exclure_sect() {

	$config = array(
		'exclure_sect' => array(),
		'tout'         => '',
		'idexplicite'  => ''
	);

	return $config;
}

/**
 * Fonction de désinstallation du plugin.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function exclure_sect_vider_tables($nom_meta_base_version) {

	// On efface la configuration spécifique du plugin
	include_spip('inc/config');
	if (lire_config('secteur')){
		effacer_config('secteur');
	}

	// Puis la meta de version du schéma du plugin.
	effacer_meta($nom_meta_base_version);
}
