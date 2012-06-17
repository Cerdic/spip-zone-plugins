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
function soapsympa_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
	      //$config = lire_config('soapsympa');
	      $config = unserialize($GLOBALS['meta']['soapsympa']);
		if (!is_array($config)) {
			$config = array();
		}
		$config = array_merge(array(
				'serveur_distant' => 'http://listes.archivistes.org/sympa/wsdl',
				'remote_host' => 'listes.archivistes.org',
				'identifiant' => 'SPIP_archivistes_org',
				'mot_de_passe' => 'archi@vistes#ORG',
				//'robot' => 'sympa.archivistes.org',
				'proprietaire' => 'delegation_generale@archivistes.org',
		), $config);
		ecrire_meta('soapsympa', serialize($config));
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
	}
}

/**
 * Fonction de desinstallation
 * On efface uniquement la méta d'installation
 *
 * @param float $nom_meta_base_version
 */
function soapsympa_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}

?>