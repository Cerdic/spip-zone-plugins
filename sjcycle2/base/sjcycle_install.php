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
function sjcycle_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.5;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		$config = lire_config('sjcycle');
		if (!is_array($config)) {
			$config = array();
		}
		$config = array_merge(array(
				'tooltip' => '',
				'tooltip_carac' => '',
				'fancy' => '',
				'fx' => 'fade',
				'sync' => 'on',
				'speed' => '2000',
				'timeout' => '4000',
				'pause' => '',
				'random' => '',
				'div_class' => 'dsjcycle',
				'div_width' => '400',
				'div_height' => '400',
				'div_margin' => '0',
				'img_bordure' => '0',
				'div_background' => 'ffffff',
				'img_position' => 'center',
				'img_width' => '400',
				'img_height' => '400',
				'img_background' => 'ffffff',
				'afficher_aide' => 'on'
		), $config);
		ecrire_meta('sjcycle', serialize($config));
		ecrire_meta($nom_meta_base_version,$current_version='0.5','non');
	}
}


/**
 * Fonction de desinstallation
 * On efface uniquement la méta d'installation
 *
 * @param float $nom_meta_base_version
 */
function sjcycle_vider_tables($nom_meta_base_version) {
	effacer_meta('sjcycle');
	effacer_meta($nom_meta_base_version);
}

?>