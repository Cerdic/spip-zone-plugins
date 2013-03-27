<?php
/**
 * Fichier d'installation / upgrade et désinstallation du plugin Multilang
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
function multilang_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			$config = lire_config('multilang');
			if (!is_array($config)) {
				$config = array();
			}
			$config = array_merge(array(
					'siteconfig' => 'on',
					'article' => '',
					'breve' => '',
					'rubrique' => 'on',
					'auteur' => 'on',
					'document' => 'on',
					'motcle' => '',
					'site' => '',
					'evenement' => ''
			), $config);
			ecrire_meta('multilang', serialize($config));
			ecrire_meta($nom_meta_base_version,$current_version='0.1','non');
	}
}

/**
 * Fonction de desinstallation
 * On efface uniquement la méta d'installation
 *
 * @param float $nom_meta_base_version
 */
function multilang_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}

?>