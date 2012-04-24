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
function paypal_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		$config = lire_config('paypal');
		if (!is_array($config)) {
			$config = array();
		}
		$config = array_merge(array(
				'environnement' => 'test',
				'currency_code' => 'EUR',
				'account_prod' => '',
				'account_test' => '',
		), $config);
		// On essaye de recupere la config mal rangee si elle existe
		if($version_cible == '0.1') {
			$old_config = lire_config('paypal_api_prod') ;
			if (is_array($old_config) && $old_config['account']!='') {
				$config['account_prod'] = $old_config['account'];
			}
			$old_config = lire_config('paypal_api_test') ;
			if (is_array($old_config) && $old_config['account']!='') {
				$config['account_test'] = $old_config['account'];
			}
			unset($config['api']) ;
			unset($config['soumission']) ;
			unset($config['tax']) ;
			effacer_meta('paypal_api_prod');
			effacer_meta('paypal_api_test');
		}

		ecrire_meta('paypal', serialize($config));
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
	}
}


/**
 * Fonction de desinstallation
 * On efface uniquement la meta d'installation
 *
 * @param float $nom_meta_base_version
 */
function paypal_vider_tables($nom_meta_base_version) {
	effacer_meta('paypal');
	effacer_meta($nom_meta_base_version);
}

?>