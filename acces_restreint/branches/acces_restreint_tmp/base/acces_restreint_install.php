<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
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
function accesrestreint_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/acces_restreint');
		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			// ajout des champs publique/privee si pas existants
			$desc = sql_showtable("spip_zones", true);
			if (!isset($desc['field']['publique']))
				sql_alter("TABLE spip_zones ADD publique ENUM('non', 'oui') DEFAULT 'oui' NOT NULL AFTER descriptif");
			if (!isset($desc['field']['privee']))
				sql_alter("TABLE spip_zones ADD privee ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER publique");
			echo "AccesRestreint Install<br/>";
			effacer_meta($nom_meta_base_version); // salade de majuscules
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if (version_compare($current_version,'0.2','<')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			// ajout des champs publique/privee si pas existants
			$desc = sql_showtable("spip_zones", true);
			if (!isset($desc['field']['publique']))
				sql_alter("TABLE spip_zones ADD publique ENUM('non', 'oui') DEFAULT 'oui' NOT NULL AFTER descriptif");
			if (!isset($desc['field']['privee']))
				sql_alter("TABLE spip_zones ADD privee ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER publique");
			echo "AccesRestreint@0.2<br />";
			ecrire_meta($nom_meta_base_version,$current_version='0.2','non');
		}
		if (version_compare($current_version,'0.3','<')){
			sql_alter("TABLE `zones_auteurs` DROP INDEX `id_zone`");
			sql_alter("TABLE `zones_auteurs` ADD PRIMARY KEY ( `id_zone` , `id_auteur` )");
			sql_alter("TABLE `zones_rubriques` DROP INDEX `id_zone`");
			sql_alter("TABLE `zones_rubriques` ADD PRIMARY KEY ( `id_zone` , `id_rubrique` )");
			echo "AccesRestreint@0.3<br />";
			ecrire_meta($nom_meta_base_version,$current_version='0.3','non');
		}
		if (version_compare($current_version,'0.3.0.1','<')){
			#ecrire_meta('creer_htaccess','oui');
			echo "AccesRestreint@0.3.0.1<br />";
			ecrire_meta($nom_meta_base_version,$current_version='0.3.0.1','non');
		}
		if (version_compare($current_version,'0.3.0.2','<')){
			#ecrire_meta('creer_htaccess','oui');
			sql_alter("TABLE spip_zone ALTER titre SET DEFAULT ''");
			sql_alter("TABLE spip_zone ALTER descriptif SET DEFAULT ''");
			echo "AccesRestreint@0.3.0.2<br />";
			ecrire_meta($nom_meta_base_version,$current_version='0.3.0.2','non');
		}
	}
}

/**
 * Fonction de desinstallation
 *
 * @param unknown_type $nom_meta_base_version
 */
function accesrestreint_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_zones");
	sql_drop_table("spip_zones_auteurs");
	sql_drop_table("spip_zones_rubriques");
	effacer_meta('creer_htaccess');
	effacer_meta($nom_meta_base_version);
}

?>
