<?php
/**
 * Plugin Catalogue pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
 *
 */

include_spip('inc/meta');
include_spip('base/create');
include_spip('base/catalogue_peupler_base');


function catalogue_upgrade($nom_meta_base_version, $version_cible){
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version=="0.0") {
		creer_base();
		catalogue_peupler_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}
	if (version_compare($current_version,"1.6","<")){
		maj_tables('spip_variantes');
		maj_tables('spip_options');
		maj_tables('spip_options_articles');
		maj_tables('spip_transactions');
		maj_tables('spip_lignes_transactions');
				
		ecrire_meta($nom_meta_base_version,$current_version="1.6");
	}
}


function catalogue_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_lignes_transactions");
	sql_drop_table("spip_transactions");
	sql_drop_table("spip_options_articles");
	sql_drop_table("spip_options");
	sql_drop_table("spip_variantes");

	effacer_meta($nom_meta_base_version);
}

?>