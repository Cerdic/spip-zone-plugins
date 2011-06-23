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
function forumsectorise_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		$config = lire_config('forumsectorise');
		if (!is_array($config)) {
			$config = array();
		}
		$config = array_merge(array(
				'id_secteur' => '',
				'type' => 'non',
				'option' => 'tous'
		), $config);
		ecrire_meta('forumsectorise', serialize($config));
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
	}
}


/**
 * Fonction de desinstallation
 * On efface uniquement la méta d'installation
 *
 * @param float $nom_meta_base_version
 */
function forumsectorise_vider_tables($nom_meta_base_version) {
	effacer_meta('forumsectorise');
	effacer_meta($nom_meta_base_version);
}

?>