<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin SPIPr-Dane-Noisettes
 *
 * @plugin     SPIPr-Dane-Noisettes
 * @copyright  2019
 * @author     Dominique Lepaisant
 * @licence    GNU/GPL
 * @package    SPIP\Sdn\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin SPIPr-Dane-Noisettes.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function sdn_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

    // Configurations du noizetier par défaut
	$config_noizetier_sdn = array(
		'objets_noisettes' => array('spip_articles','spip_rubriques'),
		'encapsulation_noisette' => '',
		'ajax_noisette' => '',
		'inclusion_dynamique_noisette' => '',
		'profondeur_max' => '',
	);
    // Configurations de socialtags par défaut
	$config_socialtags_sdn = array(
		'tags' => array('facebook','twitter'),
		'jsselector' => '#socialtags',
		'afterorappend' => 'after',
		'wopen' => 'non',
	);


	$maj['create'] = array(
		array('ecrire_config', 'noizetier', $config_noizetier_sdn),
		array('ecrire_config', 'socialtags', $config_socialtags_sdn),
	);


	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin SPIPr-Dane-Noisettes.
 * 
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function sdn_vider_tables($nom_meta_base_version) {

	include_spip('inc/config');
	effacer_config('sdn');
	effacer_meta($nom_meta_base_version);
}
