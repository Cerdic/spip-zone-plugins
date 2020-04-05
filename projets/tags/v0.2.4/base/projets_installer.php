<?php
/**
 * Plugin SPIP-Projet
 * Licence GPL
 * Eric Lupinacci, Quentin Drouet
 *
 * Installation et upgrade des tables
 *
 */
include_spip('inc/meta');
include_spip('base/create');

function projets_upgrade($nom_meta_base_version,$version_cible){
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];

	if ($current_version=="0.0") {
		include_spip('base/projets_declarer');
		creer_base();
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
	}
}

function projets_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_projets");
	sql_drop_table("spip_projets_liens");
	effacer_meta($nom_meta_base_version);
}
?>
