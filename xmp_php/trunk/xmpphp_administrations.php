<?php
/**
 * XMP php
 * Récupération des métadonnées XMP
 *
 * Auteur : kent1
 * ©2011 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

/**
 * Action d'installation et de mise à jour
 * @param float $nom_meta_version_base
 * @param float $version_cible
 */
function xmpphp_upgrade($nom_meta_version_base, $version_cible){

	$version_actuelle = '0.0';
	if (
		(!isset($GLOBALS['meta'][$nom_meta_version_base]))
		|| (($version_actuelle = $GLOBALS['meta'][$nom_meta_version_base]) != $version_cible)
	){
		include_spip('base/xmpphp');
		include_spip('base/create');
		include_spip('base/abstract_sql');
		if (version_compare($version_actuelle,'0.0','<=')){
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
		}
	}
}

/**
 * Action de désinstallation
 * -* Effacer la configuration
 * -* Effacer la liste des sites dispo
 * -* Effacer la meta de version
 * 
 * @param float $nom_meta_version_base
 */
function xmpphp_vider_tables($nom_meta_version_base){
	effacer_meta($nom_meta_version_base);
}

?>