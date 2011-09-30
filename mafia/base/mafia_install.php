<?php
/**
 * Plugin Mafia pour Spip 2.0
 * Licence GPL (c) 2011 Anne-lise Martenot
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');
/**
 * Fonction d'installation, mise a jour de la base
 *
 * @param unknown_type $nom_meta_base_version
 * @param unknown_type $version_cible
 */
function mafia_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/mafia');
		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/create');
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
	}
}

/**
 * Fonction de desinstallation
 *
 * @param unknown_type $nom_meta_base_version
 */
function mafia_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_mafias");
	effacer_meta($nom_meta_base_version);
}

?>
