<?php
/**
 * Plugin Comptes pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 */

include_spip('inc/meta');
include_spip('base/create');
include_spip('base/peupler_base');


function comptes_upgrade($nom_meta_base_version, $version_cible){
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version=="0.0") {
		creer_base();
		peupler_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}
	if (version_compare($current_version,"1.6","<")){
		maj_tables('spip_comptes');
		maj_tables('spip_contacts');
		maj_tables('spip_champs');
		maj_tables('spip_champs_liens');
		maj_tables('spip_numeros');
		maj_tables('spip_numeros_liens');
		maj_tables('spip_adresses');
		maj_tables('spip_adresses_liens');
		
		ecrire_meta($nom_meta_base_version,$current_version="1.0");
	}
}


function comptes_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_comptes");
	sql_drop_table("spip_contacts");
	sql_drop_table("spip_champs");
	sql_drop_table("spip_champs_liens");
	sql_drop_table("spip_numeros");
	sql_drop_table("spip_numeros_liens");
	sql_drop_table("spip_adresses");
	sql_drop_table("spip_adresses_liens");

	effacer_meta($nom_meta_base_version);
}

?>