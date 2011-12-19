<?php
/**
 * Plugin commandes pour Spip 2.1
 * Licence GPL
 * Cyril MARION - Ateliers CYM http://www.cym.fr
 *
 */

include_spip('inc/meta');
include_spip('base/create');

function commandes_upgrade($nom_meta_base_version, $version_cible){
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version=="0.0") {
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}
	// ajout de objet/id_objet sur les details de commande
	if (version_compare($current_version,"0.2","<")){
		maj_tables('spip_commandes_details');
		ecrire_meta($nom_meta_base_version, $current_version="0.2");
	}
}


function commandes_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_commandes");
	sql_drop_table("spip_commandes_details");
	effacer_meta($nom_meta_base_version);
}



?>
