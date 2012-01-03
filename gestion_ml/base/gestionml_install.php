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
function gestionml_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		$config = lire_config('gestionml');
		if (!is_array($config)) {
			$config = array();
		}
		$config = array_merge(array(
				'hebergeur' => '0',
				'serveur_distant' => 'https://www.ovh.com/soapi/soapi-re-1.28.wsdl',
				'domaine' => 'mondomaine.tld',
				'identifiant' => '',
				'mot_de_passe' => '',
		), $config);
		ecrire_meta('gestionml', serialize($config));
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
	}
}

/**
 * Fonction de desinstallation
 * On efface uniquement la méta d'installation
 *
 * @param float $nom_meta_base_version
 */
function gestionml_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}

?>