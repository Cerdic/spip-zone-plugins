<?php
/**
 * Fichier d'installation / upgrade et désinstallation du plugin jquerysuperfish
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
function jquerysuperfish_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		$config = lire_config('jquerysuperfish');
		if (!is_array($config)) {
			$config = array();
		}
		$config = array_merge(array(
				'menu_hori' => '',
				'menu_vert' => '',
				'menu_navbar' => '',
				'supersubs' => ''
		), $config);
		ecrire_meta('jquerysuperfish', serialize($config));
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
	}
}


/**
 * Fonction de desinstallation
 * On efface uniquement la méta d'installation
 *
 * @param float $nom_meta_base_version
 */
function jquerysuperfish_vider_tables($nom_meta_base_version) {
	effacer_meta('jquerysuperfish');
	effacer_meta($nom_meta_base_version);
}

?>