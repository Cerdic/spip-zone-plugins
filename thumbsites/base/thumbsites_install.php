<?php
/**
 * Fichier d'installation / upgrade et désinstallation du plugin
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

/**
 * Fonction d'upgrade/maj
 * On crée une configuration par défaut
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function thumbsites_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		$config = lire_config('thumbsites');
		if (!is_array($config)) {
			$config = array();
		}
		$config = array_merge(array(
				'serveur' => 'thumbshots',
				'girafa_identifiant' => '',
				'girafa_signature' => '',
				'websnapr_clef' => '',
				'websnapr_taille' => 'T',
				'robothumb_taille' => '100x75',
				'miwin_taille' => '80X60',
				'apercite_taille' => '120X90',
				'duree_cache' => '30'
		), $config);
		ecrire_meta('thumbsites', serialize($config));
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
	}
}


/**
 * Fonction de desinstallation
 * On efface uniquement la méta d'installation
 *
 * @param float $nom_meta_base_version
 */
function thumbsites_vider_tables($nom_meta_base_version) {
	effacer_meta('thumbsites');
	effacer_meta($nom_meta_base_version);
}

?>