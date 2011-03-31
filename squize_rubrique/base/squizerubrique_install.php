<?php
/**
 * Fichier d'installation / upgrade et désinstallation
 */

include_spip('inc/meta');

/**
 * Fonction d'upgrade/maj
 * On crée une configuration par défaut
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function squizerubrique_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.1;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		$config = lire_config('squizerubrique');
		if (!is_array($config)) {
			$config = array();
		}
		$config = array_merge(array(
				'article_accueil' => 'on',
				'article_unique' => 'on',
				'aucun_article' => '',
				'aucun_article_tri' => 'num titre',
				'aucun_article_senstri' => 'direct'
		), $config);
		ecrire_meta('squizerubrique', serialize($config));
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
	}
}


/**
 * Fonction de desinstallation
 * On efface uniquement la méta d'installation
 *
 * @param float $nom_meta_base_version
 */
function squizerubrique_vider_tables($nom_meta_base_version) {
	effacer_meta('squizerubrique');
	effacer_meta($nom_meta_base_version);
}

?>