<?php
/**
 * Plugin Torrent pour Spip
 * Licence GPL (c) 2008
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');
/**
 * Fonction d'installation, mise a jour de la base
 *
 * @param unknown_type $nom_meta_base_version
 * @param unknown_type $version_cible
 */
function torrent_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/acces_restreint');
		if (version_compare($current_version,'0.1','<=')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
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
function torrent_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_torrents");
	effacer_meta($nom_meta_base_version);
}
