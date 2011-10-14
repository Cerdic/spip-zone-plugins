<?php
/**
 * Plugin mots-objets pour Spip 2.0
 * Licence GPL 
 * Adaptation Cyril MARION - (c) 2010 Ateliers CYM http://www.cym.fr
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');
include_spip('base/create');

function mots_objets_upgrade($nom_meta_base_version, $version_cible){

	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version=="0.0") {
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}
	// documents...
	if (version_compare($current_version,"0.2","<")){
		creer_base();
		ecrire_meta($nom_meta_base_version,$current_version="0.2");
	}

}
function mots_objets_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_mots_auteurs");
	sql_drop_table("spip_mots_documents");
	effacer_meta($nom_meta_base_version);
}



?>
