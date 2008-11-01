<?php
/*
 * Plugin FBLogin / gestion du login FB
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */


if (!defined("_ECRIRE_INC_VERSION")) return;
function fblogin_declarer_tables_principales($tables_principales){

	$tables_principales['spip_auteurs']['field']['fb_uid'] = "bigint(21) NOT NULL";
	return $tables_principales;
}

	
/**
 * MAJ/Upgrade de la base
 *
 * @param unknown_type $nom_meta_base_version
 * @param unknown_type $version_cible
 */
function fblogin_upgrade($nom_meta_base_version,$version_cible){
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.1.0','<')){
			sql_alter('table spip_auteurs ADD fb_uid bigint(21) NOT NULL');
			ecrire_meta($nom_meta_base_version,$current_version='0.101');
		}
	}
}

/**
 * Suppression des tables lors de la desinstallation
 *
 * @param unknown_type $nom_meta_base_version
 */
function fblogin_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	sql_alter('table spip_auteurs drop fb_uid');
	effacer_meta($nom_meta_base_version);
}

?>