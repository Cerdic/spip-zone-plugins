<?php
/**
 * Plugin factures - Facturer avec Spip 2.0
 * Licence GPL (c) 2010
 * par Cyril Marion - Camille Lafitte
 */

function factures_upgrade($nom_meta_base_version, $version_cible){
	include_spip('inc/meta');
	
	/**
	 *
	 *  11/10/2010 : creation
	 *
	 */
	 
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version=="0.0") {
		include_spip('base/create');
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}

}

function factures_vider_tables($nom_meta_base_version) {
	
	sql_drop_table("spip_factures");
	sql_drop_table("spip_lignes_factures");

	effacer_meta($nom_meta_base_version);
}

?>