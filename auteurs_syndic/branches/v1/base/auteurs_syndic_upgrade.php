<?php
/**
 * Plugin auteurs_syndic
 * par kent1
 * Installation / Mise à jour et désinstallation
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');
include_spip('base/create');

/**
 * 
 * Fonction d'installation et de mise à jour
 * @param unknown_type $nom_meta_version_base
 * @param unknown_type $version_cible
 */
function auteurs_syndic_upgrade($nom_meta_version_base, $version_cible){

	$version_actuelle = '0.0';
	if (
		(!isset($GLOBALS['meta'][$nom_meta_version_base]))
		|| (($version_actuelle = $GLOBALS['meta'][$nom_meta_version_base]) != $version_cible)
	){
		if (version_compare($version_actuelle,'0.0','=')){
			// Création des tables
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
		}
	}
}

/**
 * 
 * Fonction de désinstallation
 * @param $nom_meta_version_base
 */
function auteurs_syndic_vider_tables($nom_meta_version_base){
		
	// On efface la version entregistrée
	effacer_meta($nom_meta_version_base);

}
?>