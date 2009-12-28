<?php
/**
 * Plugin Catalogue pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
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
	if (version_compare($current_version,"1.3","<")){
		// ajout fictif
		maj_tables('cat_familles');
		maj_tables('cat_produits');
		maj_tables('cat_variantes');				
		ecrire_meta($nom_meta_base_version,$current_version="1.3");
	}
}


function catalogue_vider_tables($nom_meta_base_version) {
	sql_drop_table("cat_variantes");
	sql_drop_table("cat_produits");
	sql_drop_table("cat_familles");
	sql_drop_table("variantes");
	sql_drop_table("produits");
	sql_drop_table("familles");
	sql_drop_table("spip_cat_variantes");
	sql_drop_table("spip_cat_produits");
	sql_drop_table("spip_cat_familles");
	sql_drop_table("toto");
	sql_drop_table("titi");
	sql_drop_table("tata");
	effacer_meta($nom_meta_base_version);
}

?>