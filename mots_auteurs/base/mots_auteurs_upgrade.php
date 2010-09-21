<?php
/**
 * Plugin mots-auteurs pour Spip 2.0
 * Licence GPL 2010
 * Adaptation Cyril MARION - Ateliers CYM http://www.cym.fr
 *
 */

include_spip('inc/meta');
include_spip('base/create');

function mots_auteurs_upgrade($nom_meta_base_version, $version_cible){

	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version=="0.0") {
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}

}
function mots_auteurs_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_mots_auteurs");
	effacer_meta($nom_meta_base_version);
}



?>