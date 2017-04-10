<?php
/**
 * Fichier d'installation / upgrade et désinstallation du plugin Manuel site
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
function manuelsite_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
		|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		$config = lire_config('manuelsite',array());
		if (!is_array($config))
			$config = array();
		
		$url_contrib = "https://contrib.spip.net/?article2986";
		$config = array_merge(array(
				'id_article' => '0',
				'cacher_public' => '',
				'intro' => _T('manuelsite:intro',array('url'=>$url_contrib)),
				'email' => '',
				'afficher_bord_gauche' => 'on'
		), $config);
		ecrire_meta('manuelsite', serialize($config));
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
	}
}

/**
 * Fonction de desinstallation
 * On efface uniquement la méta d'installation
 *
 * @param float $nom_meta_base_version
 */
function manuelsite_vider_tables($nom_meta_base_version) {
	effacer_meta('manuelsite');
	effacer_meta($nom_meta_base_version);
}

?>