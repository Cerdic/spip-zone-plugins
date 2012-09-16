<?php
/**
 * Fichier d'installation / upgrade et désinstallation du plugin
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction d'upgrade/maj
 * On crée une configuration par défaut
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function thumbsites_upgrade($nom_meta_base_version,$version_cible){
	include_spip('inc/config');
    $maj = array();
	$maj['create'] = array(
		array(
			'serveur' => 'thumbshots',
			'websnapr_clef' => '',
			'websnapr_taille' => 'T',
			'robothumb_taille' => '100x75',
			'miwim_taille' => '120x90',
			'apercite_taille' => '120X90',
			'duree_cache' => '30'
		)
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de desinstallation
 * On efface uniquement la méta d'installation
 *
 * @param float $nom_meta_base_version
 */
function thumbsites_vider_tables($nom_meta_base_version) {
//	effacer_meta('thumbsites');
	effacer_meta($nom_meta_base_version);
}

?>