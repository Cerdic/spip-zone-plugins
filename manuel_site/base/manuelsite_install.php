<?php
/**
 * Fichier d'installation / upgrade et désinstallation du plugin sjcycle
 */

include_spip('inc/meta');

/**
 * Fonction d'upgrade/maj
 * On crée une configuration par défaut
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function manuelsite_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.1;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		$config = lire_config('manuelsite');
		if (!is_array($config)) {
			$config = array();
		}
		$config = array_merge(array(
				'id_article' => '0',
				'intro' => '',
				'email' => '',
				'largeur' => '300',
				'background_color' => '#D6DDE5'
		), $config);
		ecrire_meta('manuelsite', serialize($config));
		ecrire_meta($nom_meta_base_version,$current_version='0.1','non');
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