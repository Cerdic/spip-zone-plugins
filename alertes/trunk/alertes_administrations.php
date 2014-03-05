<?php
/*
 * Plugin Alertes
 * Distribué sous licence GPL
 *
 */
 
/*** Installation et desinstallation ***/
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Creation/Upgrade des tables
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function alertes_upgrade($nom_meta_base_version,$version_cible){
	include_spip('inc/meta');
	$current_version = "0.0.0";
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'1.0.1','<')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			include_spip('base/serial');
			creer_ou_upgrader_table("spip_alertes",$GLOBALS['tables_principales']['spip_alertes'],true);
			creer_ou_upgrader_table("spip_alertes_cron",$GLOBALS['tables_principales']['spip_alertes_cron'],true);

			ecrire_meta($nom_meta_base_version,$current_version="1.0.1",'non');
		}
	}
}

/**
 * Desinstallation
 *
 * @param string $nom_meta_base_version
 */
function alertes_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	sql_drop_table("spip_alertes");
	sql_drop_table("spip_alertes_cron");
	effacer_meta($nom_meta_base_version);
}

?>