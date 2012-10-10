<?php
/**
 * Plugin Groupes pour Spip 2.0
 * Licence GPL (c) 2008 Matthieu Marcillaud
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');
/**
 * Fonction d'installation, mise a jour de la base
 *
 * @param unknown_type $nom_meta_base_version
 * @param unknown_type $version_cible
 */
function Grappes_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/grappes');
		include_spip('base/create');
		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if (version_compare($current_version,'0.2','<')){
			include_spip('base/abstract_sql');
			maj_tables('spip_grappes');
			ecrire_meta($nom_meta_base_version,$current_version='0.2','non');
		}
		if (version_compare($current_version,'0.2.1','<')){
			include_spip('base/abstract_sql');
			maj_tables('spip_grappes_liens');
			maj_tables('spip_grappes');
			ecrire_meta($nom_meta_base_version,$current_version='0.2.1','non');
		}
	}
}

/**
 * Fonction de desinstallation
 *
 * @param unknown_type $nom_meta_base_version
 */
function Grappes_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_grappes");
	sql_drop_table("spip_grappes_liens");
	effacer_meta($nom_meta_base_version);
}

?>
