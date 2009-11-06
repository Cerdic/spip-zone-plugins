<?php
/**
 * Plugin Tradlang
 * Licence GPL (c) 2009 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

/**
 * Fonction d'installation, mise a jour de la base
 *
 * @param unknown_type $nom_meta_base_version
 * @param unknown_type $version_cible
 */
function tradlang_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/tradlang');
		include_spip('base/create');
		if (version_compare($current_version,'0.0','<=')){
			creer_base();
			echo "Installation des tables de tradlang r&eacute;alis&eacute;e<br/>";
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if (version_compare($current_version,'0.2','<=')){
			sql_alter('TABLE spip_tradlang DROP PRIMARY KEY');
			sql_alter('TABLE `spip_tradlang` ADD `id_tradlang` BIGINT(21) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
			sql_alter('TABLE `spip_tradlang` ADD UNIQUE (`id`,`module`,`lang`)');
			echo "Modification des cl&eacute;s de la table tradlang<br/>";
			ecrire_meta($nom_meta_base_version,$current_version='0.2','non');
		}
		if (version_compare($current_version,'0.3','<=')){
			maj_tables ('spip_tradlang_modules');
			echo "Upgrade des tables de Tradlang en version 0.3<br/>";
			ecrire_meta($nom_meta_base_version,$current_version='0.3','non');
		}
	}
}

/**
 * Fonction de desinstallation
 *
 * @param unknown_type $nom_meta_base_version
 */
function tradlang_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_tradlang");
	sql_drop_table("spip_tradlang_modules");
	effacer_meta($nom_meta_base_version);
}

?>