<?php
/**
 * Plugin tradrub
 * Licence GPL (c) 2008-2010 Stephane Laurent (Bill), Matthieu Marcillaud
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

/**
 * Upgrade de la base
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function tradrub_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;

	if ( (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
		|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible))
	{
		include_spip('base/tradrub');
		if ($current_version==0.0){
			include_spip('base/create');
			maj_tables('spip_rubriques');
			// index sur le nouveau champ
			sql_alter("TABLE spip_rubriques ADD INDEX (id_trad)");
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}	
	}
}

/**
 * Desinstallation du plugin
 *
 * @param string $nom_meta_base_version
 */
function tradrub_vider_tables($nom_meta_base_version) {
	sql_alter("TABLE spip_rubriques DROP id_trad");
	effacer_meta($nom_meta_base_version);
}
	

?>
