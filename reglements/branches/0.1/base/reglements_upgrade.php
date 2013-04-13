<?php
/**
 * Plugin reglements - Facturer avec Spip 2.0
 * Licence GPL (c) 2010
 * par Cyril Marion - Camille Lafitte
 */
 
include_spip('inc/meta');
include_spip('base/create');

function reglements_upgrade($nom_meta_base_version, $version_cible){	
	 
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version=="0.0") {
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}
}

function reglements_vider_tables($nom_meta_base_version) {
	
	sql_drop_table("spip_reglements");
	effacer_meta($nom_meta_base_version);

}

?>
