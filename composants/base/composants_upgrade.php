<?php

/*
 * Plugin Composants
 * Licence GPL (c) 2011 Cyril Marion
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function composants_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/composants');
		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version='0.1','non');
		}
	}
}

/**
 * Fonction de desinstallation
 *
 * @param unknown_type $nom_meta_base_version
 */
function composants_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_composants");
	sql_drop_table("spip_composants_projets");
	effacer_meta('composants');
	effacer_meta($nom_meta_base_version);
}


?>