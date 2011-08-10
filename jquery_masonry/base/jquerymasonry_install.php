<?php
/**
 * Fichier d'installation / upgrade et désinstallation du plugin Spip Jquery Masonry
 */

include_spip('inc/meta');

/**
 * Fonction d'upgrade/maj
 * On crée une configuration par défaut
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function jquerymasonry_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		$config = lire_config('jquerymasonry');
		if (!is_array($config)) {
			$config = array();
		}
		$config = array_merge(array(
				'nombre' => '1',
		), $config);
		ecrire_meta('jquerymasonry', serialize($config));
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
	}
}


/**
 * Fonction de desinstallation
 * On efface uniquement la méta d'installation
 *
 * @param float $nom_meta_base_version
 */
function jquerymasonry_vider_tables($nom_meta_base_version) {
	effacer_meta('jquerymasonry');
	effacer_meta($nom_meta_base_version);
}

?>