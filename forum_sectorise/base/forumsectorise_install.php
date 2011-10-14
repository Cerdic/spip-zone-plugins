<?php
/**
 * Fichier d'installation / upgrade et désinstallation du plugin sjcycle
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
function forumsectorise_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.0','=')){
			$config = array(
								'id_secteur' => '',
								'type' => 'non',
								'option' => 'tous'
							);
			ecrire_meta($nom_meta_base_version, $current_version="0.0", 'non');
		}

		if (version_compare($current_version,'0.2','<=')){	
			if (!isset($config)) {
				$config = lire_config('forumsectorise');
			}
			// On peut maintenant selectionner plusieurs secteurs avec la saisie secteur
			$config['id_secteur'] = array( 0 => $config['id_secteur'] );
			ecrire_meta($nom_meta_base_version, $current_version="0.2", 'non');
			echo _T('forumsectorise:msg_maj_version', array('version'=>$current_version))."<br/>";
		}

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